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
        $cuestionario = Cuestionario::findOrFail($id_cuestionario); // Asegura que existe
        $preguntasDelCuestionario = $cuestionario->preguntas()->with('opciones')->get();

        // --- Validación ---
        $rules = [];
        $messages = [];
        foreach ($preguntasDelCuestionario as $pregunta) {
            //Si la pregunta es tipo checkbox, acepta varias opciones.
            if ($pregunta->tipo_input === 'checkbox') {
                $rules['respuestas.' . $pregunta->id] = 'nullable|array'; // Permite no enviar nada si no es obligatorio, o espera un array
                //Para otros tipos (radio, text, textarea), se marcan como obligatorias.
            } else { // text, radio, textarea
                $rules['respuestas.' . $pregunta->id] = 'required';
                $messages['respuestas.' . $pregunta->id . '.required'] = 'La pregunta "' . htmlspecialchars(Str::limit($pregunta->enunciado, 50)) . '" es obligatoria.';
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        //Si la validación falla, redirige de nuevo a la vista del cuestionario con errores y los datos anteriores (withInput()).
        if ($validator->fails()) {
            return redirect()->route('cuestionarios.mostrarParaNick', ['nick' => $nick, 'id_cuestionario' => $id_cuestionario])
                ->withErrors($validator)
                ->withInput();
        }
        //Busca al usuario por su Nombre (que es el nick).
        $usuario = Usuario::where('Nombre', $nick)->first();
        //Obtiene su Codigo, que se guarda con el envío.
        $usuarioCodigo = $usuario ? $usuario->Codigo : null;
        //Crea un nuevo registro en la tabla cuestionario_envios, que sirve como "formulario enviado".
        $envio = CuestionarioEnvio::create([
            'usuario_codigo' => $usuarioCodigo,
            'cuestionario_id' => $id_cuestionario,
            'nick_utilizado' => $nick,
        ]);

        $respuestasEnviadas = $request->input('respuestas', []); // Contiene todas las respuestas {id_pregunta => valor}

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

        $recomendacionCreada = null;
        $justificacionDetalle = "Hemos seleccionado este producto basado en tus respuestas generales.";
        /* Al ser un prototipo las recomendaciones estan basadas a raiz de dos preguntas  la primera es el tipo de producto, y la otra es el tipo de pelo*/
        // Busca el objeto Pregunta por su enunciado exacto.
        $preguntaCategoriaProductoObj = Pregunta::where('enunciado', '¿Qué producto quieres que te recomendemos?')->first();
        $idPreguntaCategoriaProducto = $preguntaCategoriaProductoObj ? $preguntaCategoriaProductoObj->id : null;
        $categoriaProductoElegida = null; // Valor de la respuesta, ej: 'champu', 'aceite', 'todo'
        $textoCategoriaElegida = 'cualquier tipo de producto'; // Para la justificación

        if ($idPreguntaCategoriaProducto && isset($respuestasEnviadas[$idPreguntaCategoriaProducto])) {
            $categoriaProductoElegida = $respuestasEnviadas[$idPreguntaCategoriaProducto];
            if ($categoriaProductoElegida !== 'todo') {
                // Busca la opción de pregunta para obtener el texto descriptivo (ej. "Champú")
                $opcionSeleccionada = $preguntaCategoriaProductoObj->opciones()->where('valor_opcion', $categoriaProductoElegida)->first();
                $textoCategoriaElegida = $opcionSeleccionada ? htmlspecialchars($opcionSeleccionada->texto_opcion) : $categoriaProductoElegida;
            }
        }

        // 2. Obtener respuesta para "¿Cuál es tu tipo de cabello?"
        $preguntaTipoCabelloObj = Pregunta::where('enunciado', '¿Cuál es tu tipo de cabello?')->first();
        $idPreguntaTipoCabello = $preguntaTipoCabelloObj ? $preguntaTipoCabelloObj->id : null;
        $tipoCabelloUsuario = null; // Valor de la respuesta, ej: 'liso', 'ondulado'
        $textoTipoCabelloUsuario = 'todos los tipos de cabello'; // Para la justificación

        if ($idPreguntaTipoCabello && isset($respuestasEnviadas[$idPreguntaTipoCabello])) {
            $tipoCabelloUsuario = $respuestasEnviadas[$idPreguntaTipoCabello];
            // Busca la opción de pregunta para obtener el texto descriptivo (ej. "Liso")
            $opcionSeleccionada = $preguntaTipoCabelloObj->opciones()->where('valor_opcion', $tipoCabelloUsuario)->first();
            $textoTipoCabelloUsuario = $opcionSeleccionada ? htmlspecialchars($opcionSeleccionada->texto_opcion) : $tipoCabelloUsuario;
        }

        // 3. Construir la consulta para buscar el producto
        $queryProducto = Producto::query();

        // Filtrar por categoría de producto si se eligió una específica
        if ($categoriaProductoElegida && $categoriaProductoElegida !== 'todo') {
            $queryProducto->where('categoria', $categoriaProductoElegida);
        }

        // Filtrar por tipo de cabello usando palabras clave en la descripción
        if ($tipoCabelloUsuario) {
            // Este mapeo debe ser tan preciso como las descripciones de tus productos.
            // Se basa en cómo están redactadas las descripciones en tus datos de ejemplo.
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
                    // Si tu descripción puede decir "muy rizado" o "afro"
                    $queryProducto->where(function ($q) {
                        $q->where('descripcion', 'LIKE', '%para cabello muy rizado%')
                            ->orWhere('descripcion', 'LIKE', '%para cabello afro%');
                    });
                    $keywordParaDescripcion = null; // Evita que se aplique el where de abajo si ya se manejó aquí
                    break;
            }
        }

        // Intentar encontrar un producto con los filtros aplicados, eligiendo uno al azar si hay varios
        $productoRecomendado = $queryProducto->inRandomOrder()->first();

        // Estrategia de Fallback (Respaldo): Si no se encuentra producto con filtros específicos, ampliar búsqueda.
        // Por ejemplo, si pidió un "aceite para cabello liso" y no hay, buscar "cualquier producto para cabello liso".
        if (!$productoRecomendado && $tipoCabelloUsuario) {
            $queryFallbackTipoCabello = Producto::query(); // Nueva consulta
            $keywordParaDescripcion = ""; // Reset
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
                    $queryFallbackTipoCabello->where(function ($q) {
                        $q->where('descripcion', 'LIKE', '%para cabello muy rizado%')
                            ->orWhere('descripcion', 'LIKE', '%para cabello afro%');
                    });
                    $keywordParaDescripcion = null;
                    break;
            }
            if ($keywordParaDescripcion) {
                $queryFallbackTipoCabello->where('descripcion', 'LIKE', '%' . $keywordParaDescripcion . '%');
            }
            $productoRecomendado = $queryFallbackTipoCabello->inRandomOrder()->first(); // Intenta de nuevo solo con tipo de cabello
        }

        // Fallback general si aún no hay producto (muy poco probable si la BD tiene productos)
        if (!$productoRecomendado) {
            $productoRecomendado = Producto::inRandomOrder()->first(); // Un producto totalmente aleatorio como último recurso
        }

        // 4. Crear la recomendación si se encontró un producto
        if ($productoRecomendado) {
            // Construir justificación detallada y personalizada
            if ($tipoCabelloUsuario && ($categoriaProductoElegida && $categoriaProductoElegida !== 'todo')) {
                $justificacionDetalle = "Basado en tu selección de cabello '{$textoTipoCabelloUsuario}' y tu interés por un '{$textoCategoriaElegida}', te sugerimos este producto.";
            } elseif ($tipoCabelloUsuario) {
                $justificacionDetalle = "Considerando tu tipo de cabello '{$textoTipoCabelloUsuario}', este producto podría interesarte.";
            } elseif ($categoriaProductoElegida && $categoriaProductoElegida !== 'todo') {
                $justificacionDetalle = "Ya que buscas un '{$textoCategoriaElegida}', te recomendamos este producto.";
            }
            // Si no hay ninguna preferencia específica, se usa el mensaje general definido al inicio.

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
            // Si por alguna razón no se pudo crear una recomendación (ej. no hay productos que coincidan)
            return redirect()->route('cuestionarios.gracias', ['nick' => $nick]) // No se pasa recomendacionId
                ->with('success', '¡Cuestionario enviado con éxito, ' . htmlspecialchars($nick) . '! No pudimos generar una recomendación específica esta vez, pero valoramos tus respuestas.');
        }
    }
    // Método para mostrar la página de gracias (MODIFICADO para aceptar recomendacionId opcional)
    public function mostrarPaginaGracias($nick, $recomendacionId = null)
    {
        // Puedes pasar datos si tu vista 'gracias' los necesita
        return view('cuestionarios.gracias', ['nick' => $nick, 'recomendacionId' => $recomendacionId]);
    }
}
