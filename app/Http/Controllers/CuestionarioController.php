<?php

namespace App\Http\Controllers;

use App\Models\Cuestionario;
use App\Models\CuestionarioEnvio;
use App\Models\Respuesta;
use App\Models\Usuario;
use App\Models\Producto;
use App\Models\Recomendacion;
use App\Models\Pregunta;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; // Para la autenticación, si la usas
use Illuminate\Support\Facades\Validator; // Para validar los datos del formulario

class CuestionarioController extends Controller
{   //Obtiene todos los cuestionarios activos 
    public function listar()
    {
        $cuestionarios = Cuestionario::where('estado', 'ACTIVO')->orderBy('titulo')->get();
        return view('cuestionarios.listar', ['cuestionarios' => $cuestionarios]);
    }


    /**
     * Muestra un cuestionario específico con sus preguntas.
     * Recibe un nickname y el id del cuestionario.
     * El parámetro $id_cuestionario viene de la URL.
     */

    public function mostrarParaNick(Request $request, $nick, $id_cuestionario)
    {
        // Busca el cuestionario por su ID. Si no lo encuentra o no está ACTIVO, muestra un error .
        $cuestionario = Cuestionario::where('id', $id_cuestionario)->where('estado', 'ACTIVO')->firstOrFail();

        // Carga las preguntas asociadas a este cuestionario, ordenadas por el campo 'numero' de la tabla pivote.
        $preguntas = $cuestionario->preguntas()->with('opciones')->get();

        // Obtener el ID de la pregunta filtro para no rellenarla desde previousAnswers
        $preguntaFiltroObj = Pregunta::where('enunciado', '¿Qué producto quieres que te recomendemos?')->first();
        $idPreguntaFiltroAExcluir = $preguntaFiltroObj ? $preguntaFiltroObj->id : null;


        $respuestasPreviasFormateadas = [];

        $envioPrevioId = $request->query('envio_previo');


        if ($envioPrevioId) {
            // Cargar el envío previo con sus respuestas y la pregunta asociada a cada respuesta
            // para determinar el tipo de input (especialmente para checkboxes)
            $envioPrevio = CuestionarioEnvio::with('respuestas.pregunta')->find($envioPrevioId);
            if ($envioPrevio && $envioPrevio->cuestionario_id == $id_cuestionario) {
                foreach ($envioPrevio->respuestas as $respuesta) {
                    $preguntaId = $respuesta->id_preguntas;
                    $valorRespuesta = $respuesta->valor_pregunta;
                    // Verificar si la pregunta original existe y su tipo
                    $tipoInput = $respuesta->pregunta ? $respuesta->pregunta->tipo_input : null;

                    if ($tipoInput === 'checkbox') {
                        if (!isset($respuestasPreviasFormateadas[$preguntaId])) {
                            $respuestasPreviasFormateadas[$preguntaId] = [];
                        }
                        $respuestasPreviasFormateadas[$preguntaId][] = $valorRespuesta;
                    } else {
                        $respuestasPreviasFormateadas[$preguntaId] = $valorRespuesta;
                    }
                }
            }
        }


        // Pasa el cuestionario y sus preguntas a la vista 'cuestionarios.ver_para_nick'
        return view('cuestionarios.ver_para_nick', [
            'nick' => $nick,
            'cuestionario' => $cuestionario,
            'preguntas' => $preguntas,
            'previousAnswers' => $respuestasPreviasFormateadas,
            'idPreguntaFiltro' => $idPreguntaFiltroAExcluir, // Pasar el ID de la pregunta filtro
        ]);
    }
    /**
     * Procesa el envío de un formulario de cuestionario.
     * $id_cuestionario viene de la URL.
     * $request contiene todos los datos enviados por el formulario.
     */

    public function procesarEnvioParaNick(Request $request, $nick, $id_cuestionario)
    {
        $cuestionario = Cuestionario::findOrFail($id_cuestionario);
        $preguntasDelCuestionario = $cuestionario->preguntas()->with('opciones')->get();

        // --- Validación ---
        $rules = [];
        $messages = [];
        foreach ($preguntasDelCuestionario as $pregunta) {
            if ($pregunta->tipo_input === 'checkbox') {
                $rules['respuestas.' . $pregunta->id] = 'nullable|array';
                // Para checkboxes, también podrías querer una regla como 'min:1' si al menos una opción es requerida,
                // pero tendrías que manejarlo específicamente si la pregunta en sí es opcional vs obligatoria.
                // Por ahora, 'nullable|array' permite que sea un array o que no se envíe.
            } else { // text, radio, textarea
                $rules['respuestas.' . $pregunta->id] = 'required';
                $messages['respuestas.' . $pregunta->id . '.required'] = 'La pregunta "' . htmlspecialchars(Str::limit($pregunta->enunciado, 50)) . '" es obligatoria.';
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->route('cuestionarios.mostrarParaNick', [
                'nick' => $nick,
                'id_cuestionario' => $id_cuestionario,
                // Si estás usando ?envio_previo=ID para rellenar, puede que quieras pasarlo de nuevo aquí
                // para que el formulario mantenga esas respuestas previas en caso de error de validación.
                // 'envio_previo' => $request->input('envio_previo_hidden_field') // Necesitarías un campo hidden
            ])
                ->withErrors($validator)
                ->withInput(); // withInput() rellenará con los datos actuales enviados que fallaron la validación.
        }

        $usuario = Usuario::where('Nombre', $nick)->first();
        $usuarioCodigo = $usuario ? $usuario->Codigo : null;

        $envio = CuestionarioEnvio::create([
            'usuario_codigo' => $usuarioCodigo,
            'cuestionario_id' => $id_cuestionario,
            'nick_utilizado' => $nick,
        ]);

        $respuestasEnviadas = $request->input('respuestas', []);

        foreach ($respuestasEnviadas as $id_pregunta => $valor_respuesta_o_array) {
            if (is_array($valor_respuesta_o_array)) { // Checkbox
                foreach ($valor_respuesta_o_array as $valor_individual) {
                    Respuesta::create([
                        'envio_id' => $envio->id,
                        'id_preguntas' => $id_pregunta,
                        'valor_pregunta' => $valor_individual,
                    ]);
                }
            } else { // Radio, text, textarea
                Respuesta::create([
                    'envio_id' => $envio->id,
                    'id_preguntas' => $id_pregunta,
                    'valor_pregunta' => $valor_respuesta_o_array,
                ]);
            }
        }

        // --- Lógica de Recomendación de Producto ---
        $recomendacionCreada = null;
        $justificacionDetallePredeterminada = "Hemos seleccionado este producto basado en tus respuestas generales.";

        $preguntaCategoriaProductoObj = Pregunta::where('enunciado', '¿Qué producto quieres que te recomendemos?')->first();
        $idPreguntaCategoriaProducto = $preguntaCategoriaProductoObj ? $preguntaCategoriaProductoObj->id : null;

        $categoriaProductoElegida = null;
        $textoCategoriaElegida = 'cualquier tipo de producto';

        if ($idPreguntaCategoriaProducto && isset($respuestasEnviadas[$idPreguntaCategoriaProducto])) {
            $categoriaProductoElegida = $respuestasEnviadas[$idPreguntaCategoriaProducto];
            // Como "todo" ya no es una opción y la pregunta es requerida, $categoriaProductoElegida será un valor específico.
            $opcionSeleccionada = $preguntaCategoriaProductoObj->opciones()->where('valor_opcion', $categoriaProductoElegida)->first();
            $textoCategoriaElegida = $opcionSeleccionada ? htmlspecialchars($opcionSeleccionada->texto_opcion) : htmlspecialchars($categoriaProductoElegida);
        } else {
            // Si la pregunta es requerida, este bloque 'else' no debería alcanzarse para $categoriaProductoElegida.
            // $textoCategoriaElegida podría tener un fallback si $opcionSeleccionada es null por alguna razón.
            $textoCategoriaElegida = 'un producto específico'; // Fallback
        }


        $preguntaTipoCabelloObj = Pregunta::where('enunciado', '¿Cuál es tu tipo de cabello?')->first();
        $idPreguntaTipoCabello = $preguntaTipoCabelloObj ? $preguntaTipoCabelloObj->id : null;
        $tipoCabelloUsuario = null;
        $textoTipoCabelloUsuario = 'todos los tipos de cabello';

        if ($idPreguntaTipoCabello && isset($respuestasEnviadas[$idPreguntaTipoCabello])) {
            $tipoCabelloUsuario = $respuestasEnviadas[$idPreguntaTipoCabello];
            $opcionSeleccionada = $preguntaTipoCabelloObj->opciones()->where('valor_opcion', $tipoCabelloUsuario)->first();
            $textoTipoCabelloUsuario = $opcionSeleccionada ? ($opcionSeleccionada->texto_opcion) : ($tipoCabelloUsuario);
        }

        $queryProducto = Producto::query();

        if ($categoriaProductoElegida && $categoriaProductoElegida !== 'todo') {
            $queryProducto->where('categoria', $categoriaProductoElegida);
        }

        if ($tipoCabelloUsuario) {
            $keywordParaDescripcion = "";
            switch ($tipoCabelloUsuario) {
                case 'liso':
                    $keywordParaDescripcion = "para cabello liso";
                    break;
                case 'ondulado':
                    $keywordParaDescripcion = "para cabello ondulado";
                    break;
                case 'rizado':
                    $keywordParaDescripcion = "para cabello rizado";
                    break;
                case 'muy_rizado_afro':
                    $queryProducto->where(function ($q) {
                        $q->where('descripcion', 'LIKE', '%para cabello muy rizado%')
                            ->orWhere('descripcion', 'LIKE', '%para cabello afro%');
                    });
                    $keywordParaDescripcion = null; // Para evitar que se aplique el siguiente if
                    break;
            }
            // ***** ESTA ES LA CORRECCIÓN IMPORTANTE *****
            if ($keywordParaDescripcion) {
                $queryProducto->where('descripcion', 'LIKE', '%' . $keywordParaDescripcion . '%');
            }
            // *******************************************
        }

        $productoRecomendado = $queryProducto->inRandomOrder()->first();

        // Estrategia de Fallback
        if (!$productoRecomendado && $tipoCabelloUsuario) {
            $queryFallbackTipoCabello = Producto::query(); // Nueva consulta para el fallback
            // Asegúrate de no filtrar por categoría aquí si el objetivo es solo el tipo de cabello
            $keywordParaDescripcionFallback = "";
            switch ($tipoCabelloUsuario) {
                case 'liso':
                    $keywordParaDescripcionFallback = "para cabello liso";
                    break;
                case 'ondulado':
                    $keywordParaDescripcionFallback = "para cabello ondulado";
                    break;
                case 'rizado':
                    $keywordParaDescripcionFallback = "para cabello rizado";
                    break;
                case 'muy_rizado_afro':
                    $queryFallbackTipoCabello->where(function ($q) {
                        $q->where('descripcion', 'LIKE', '%para cabello muy rizado%')
                            ->orWhere('descripcion', 'LIKE', '%para cabello afro%');
                    });
                    $keywordParaDescripcionFallback = null;
                    break;
            }
            if ($keywordParaDescripcionFallback) {
                $queryFallbackTipoCabello->where('descripcion', 'LIKE', '%' . $keywordParaDescripcionFallback . '%');
            }
            $productoRecomendado = $queryFallbackTipoCabello->inRandomOrder()->first();
        }

        if (!$productoRecomendado) {
            $productoRecomendado = Producto::inRandomOrder()->first(); // Fallback final: un producto totalmente aleatorio
        }

        // Crear la recomendación si se encontró un producto
        if ($productoRecomendado) {
            $justificacionDetalle = $justificacionDetallePredeterminada; // Iniciar con la predeterminada
            if ($tipoCabelloUsuario && ($categoriaProductoElegida && $categoriaProductoElegida !== 'todo')) {
                $justificacionDetalle = "Basado en tu selección de cabello '{$textoTipoCabelloUsuario}' y tu interés por un '{$textoCategoriaElegida}', te sugerimos este producto.";
            } elseif ($tipoCabelloUsuario) {
                $justificacionDetalle = "Considerando tu tipo de cabello '{$textoTipoCabelloUsuario}', este producto podría interesarte.";
            } elseif ($categoriaProductoElegida && $categoriaProductoElegida !== 'todo') {
                $justificacionDetalle = "Ya que buscas un '{$textoCategoriaElegida}', te recomendamos este producto.";
            }

            $recomendacionCreada = Recomendacion::create([
                'envio_id' => $envio->id,
                'id_producto' => $productoRecomendado->idproducto,
                'justificacion_titulo' => "Una sugerencia especial para ti: " . htmlspecialchars($productoRecomendado->nombre),
                'justificacion_detalle' => $justificacionDetalle,
            ]);
        }

        if ($recomendacionCreada) {
            return redirect()->route('cuestionarios.gracias', ['nick' => $nick, 'recomendacionId' => $recomendacionCreada->id])
                ->with('success', '¡Cuestionario enviado! ' . htmlspecialchars($nick) . ', estamos preparando tu recomendación personalizada...');
        } else {
            return redirect()->route('cuestionarios.gracias', ['nick' => $nick])
                ->with('success', '¡Cuestionario enviado con éxito, ' . htmlspecialchars($nick) . '! Aunque valoramos tus respuestas, no pudimos generar una recomendación específica esta vez.');
        }
    }

    /**
     * Muestra la página de agradecimiento.
     *
     * @param  string  $nick
     * @param  int|null  $recomendacionId
     * @return \Illuminate\View\View
     */
    public function mostrarPaginaGracias($nick, $recomendacionId = null)
    {
        return view('cuestionarios.gracias', ['nick' => $nick, 'recomendacionId' => $recomendacionId]);
    }
}
