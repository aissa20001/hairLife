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
use Illuminate\Support\Str; // Para los strings, cortar enunciados y eso
use Illuminate\Support\Facades\Auth; // Auth por si acaso, aunque aquí no lo uso
use Illuminate\Support\Facades\Validator; // El Validator de Laravel, imprescindible

class CuestionarioController extends Controller
{

    //Esta es para pillar todos los cuestionarios que estén ACTIVOS y mostrarlos.
    // Así el usuario puede elegir uno.

    public function listar()
    {
        // Pillamos solo los ACTIVOS, ordenados por título, fácil.
        $cuestionarios = Cuestionario::where('estado', 'ACTIVO')->orderBy('titulo')->get();
        // Mandamos los cuestionarios a la vista 'listar'.
        return view('cuestionarios.listar', ['cuestionarios' => $cuestionarios]);
    }



    //Muestra un cuestionario específico para que un 'nick' lo rellene.
    //Le llega el nick y el ID del cuestionario por la URL.
    // También puede recibir un 'envio_previo' para recargar respuestas.

    public function mostrarParaNick(Request $request, $nick, $id_cuestionario)
    {
        // Primero, cargar el cuestionario. Si no existe o no está ACTIVO, Laravel peta (404), que es lo que quiero.
        $cuestionario = Cuestionario::where('id', $id_cuestionario)->where('estado', 'ACTIVO')->firstOrFail();

        // Cargamos las preguntas de ESTE cuestionario.
        // Importante el `with('opciones')` para no hacer mil queries después (evitar N+1).
        $preguntas = $cuestionario->preguntas()->with('opciones')->get(); // Asumo que la relación ya ordena o lo hace la tabla pivote

        // Esta pregunta de 'Qué producto...' es especial, la uso como filtro luego. Guardo su ID.
        // Así evito que se rellene automáticamente si viene de un envío previo y quiero que la elija de nuevo.
        $preguntaFiltroObj = Pregunta::where('enunciado', '¿Qué producto quieres que te recomendemos?')->first();
        $idPreguntaFiltroAExcluir = $preguntaFiltroObj ? $preguntaFiltroObj->id : null;


        // Array para guardar las respuestas si viene de un reintento o algo así.
        $respuestasPreviasFormateadas = [];

        // Miro si me pasan un `envio_previo` por la URL para precargar respuestas.
        $envioPrevioId = $request->query('envio_previo');

        // Si hay ID de envío previo...
        if ($envioPrevioId) {
            // Cargo ese envío con sus respuestas y, de cada respuesta, la pregunta a la que pertenece (para el tipo_input).
            $envioPrevio = CuestionarioEnvio::with('respuestas.pregunta')->find($envioPrevioId);

            // Me aseguro que el envío previo exista y sea de ESTE cuestionario, no de otro.
            if ($envioPrevio && $envioPrevio->cuestionario_id == $id_cuestionario) {
                // Recorro las respuestas guardadas.
                foreach ($envioPrevio->respuestas as $respuesta) {
                    $preguntaId = $respuesta->id_preguntas;
                    $valorRespuesta = $respuesta->valor_pregunta;

                    // Necesito el tipo de input para saber si es un checkbox (que puede ser array).
                    $tipoInput = $respuesta->pregunta ? $respuesta->pregunta->tipo_input : null;

                    // Ojo con los checkboxes, pueden tener varios valores. Los guardo como array.
                    if ($tipoInput === 'checkbox') {
                        if (!isset($respuestasPreviasFormateadas[$preguntaId])) {
                            $respuestasPreviasFormateadas[$preguntaId] = [];
                        }
                        $respuestasPreviasFormateadas[$preguntaId][] = $valorRespuesta;
                    } else {
                        // El resto, un solo valor y listo.
                        $respuestasPreviasFormateadas[$preguntaId] = $valorRespuesta;
                    }
                }
            }
        }

        // Mando todo a la vista `ver_para_nick`: el nick, el cuestionario, las preguntas,
        // las respuestas previas (si las hay) y el ID de la pregunta filtro.
        return view('cuestionarios.ver_para_nick', [
            'nick' => $nick,
            'cuestionario' => $cuestionario,
            'preguntas' => $preguntas,
            'previousAnswers' => $respuestasPreviasFormateadas,
            'idPreguntaFiltro' => $idPreguntaFiltroAExcluir,
        ]);
    }


    //Aquí se procesa el formulario cuando el 'nick' lo envía.
    //Valido, guardo respuestas y intento recomendar un producto.

    public function procesarEnvioParaNick(Request $request, $nick, $id_cuestionario)
    {
        // Cargo el cuestionario. Si no existe, 404.
        $cuestionario = Cuestionario::findOrFail($id_cuestionario);
        // Necesito las preguntas del cuestionario para validar.
        $preguntasDelCuestionario = $cuestionario->preguntas()->with('opciones')->get();

        // --- A validar se ha dicho ---
        $rules = [];
        $messages = [];
        // Reglas para cada pregunta.
        foreach ($preguntasDelCuestionario as $pregunta) {
            if ($pregunta->tipo_input === 'checkbox') {
                // Checkboxes pueden ser array y opcionales (nullable).
                $rules['respuestas.' . $pregunta->id] = 'nullable|array';
                // ¡OJO! Esta línea de abajo para 'required' pisa la de 'nullable|array' para checkboxes.
                // Si los checkboxes NO son siempre requeridos, esto necesitaría un 'else'.
                // Tal como está, si un checkbox no se marca, y esta línea se aplica, fallará la validación.
                // Dejándolo como estaba, pero es algo a revisar si los checkboxes son opcionales.
            }
            // Para el resto (o si la lógica anterior se pisa), que sean obligatorias.
            // Si un checkbox debe ser opcional Y tener al menos una opción si se envía algo, la lógica es más compleja.
            // Por ahora, asumo que si es checkbox y se envía algo, es un array. Si no se envía, es null.
            // La línea de abajo hace que TODOS los campos sean 'required' (incluyendo checkboxes si la lógica de arriba no tiene un 'else').
            $rules['respuestas.' . $pregunta->id] = 'required';
            $messages['respuestas.' . $pregunta->id . '.required'] = 'La pregunta "' . htmlspecialchars(Str::limit($pregunta->enunciado, 50)) . '" es obligatoria.';
        }

        // Creo el validador de Laravel.
        $validator = Validator::make($request->all(), $rules, $messages);

        // Si falla la validación, de vuelta al formulario con errores y lo que había puesto (`withInput`).
        if ($validator->fails()) {
            return redirect()->route('cuestionarios.mostrarParaNick', [
                'nick' => $nick,
                'id_cuestionario' => $id_cuestionario,
            ])
                ->withErrors($validator)
                ->withInput();
        }
        // --- Fin Validación ---

        // Busco al usuario por el nick. 'Nombre' es el campo, supongo.
        $usuario = Usuario::where('Nombre', $nick)->first();
        // Saco su 'Codigo' (ID o lo que sea que use para enlazar) si existe.
        $usuarioCodigo = $usuario ? $usuario->Codigo : null; // Ojo, `Codigo` con mayúscula, como en el original

        // Guardo el intento de envío.
        $envio = CuestionarioEnvio::create([
            'usuario_codigo' => $usuarioCodigo,
            'cuestionario_id' => $id_cuestionario,
            'nick_utilizado' => $nick,
        ]);

        // Recojo todas las respuestas del form. Vienen como un array 'respuestas[id_pregunta] => valor'.
        $respuestasEnviadas = $request->input('respuestas', []);

        // Guardo cada respuesta.
        foreach ($respuestasEnviadas as $id_pregunta => $valor_respuesta_o_array) {
            // Si es un array (checkbox múltiple), guardo cada valor por separado.
            if (is_array($valor_respuesta_o_array)) {
                foreach ($valor_respuesta_o_array as $valor_individual) {
                    Respuesta::create([
                        'envio_id' => $envio->id,
                        'id_preguntas' => $id_pregunta,
                        'valor_pregunta' => $valor_individual,
                    ]);
                }
            } else {
                // Si no, un solo registro de respuesta.
                Respuesta::create([
                    'envio_id' => $envio->id,
                    'id_preguntas' => $id_pregunta,
                    'valor_pregunta' => $valor_respuesta_o_array,
                ]);
            }
        }

        // --- Ahora la parte de recomendar un producto ---
        $recomendacionCreada = null; // Para saber si al final recomiendo algo.
        $justificacionDetallePredeterminada = "Hemos seleccionado este producto basado en tus respuestas generales.";

        // La pregunta clave para la categoría de producto.
        $preguntaCategoriaProductoObj = Pregunta::where('enunciado', '¿Qué producto quieres que te recomendemos?')->first();
        $idPreguntaCategoriaProducto = $preguntaCategoriaProductoObj ? $preguntaCategoriaProductoObj->id : null;

        $categoriaProductoElegida = null;
        $textoCategoriaElegida = 'cualquier tipo de producto'; // Para la justificación.

        // Si respondió a la categoría...
        if ($idPreguntaCategoriaProducto && isset($respuestasEnviadas[$idPreguntaCategoriaProducto])) {
            $categoriaProductoElegida = $respuestasEnviadas[$idPreguntaCategoriaProducto];
            // Intento pillar el texto de la opción para que la justificación quede más chula.
            $opcionSeleccionada = $preguntaCategoriaProductoObj->opciones()->where('valor_opcion', $categoriaProductoElegida)->first();
            $textoCategoriaElegida = $opcionSeleccionada ? htmlspecialchars($opcionSeleccionada->texto_opcion) : htmlspecialchars($categoriaProductoElegida);
        } else {
            $textoCategoriaElegida = 'un producto específico'; // Si no eligió, ajusto el texto.
        }

        // Lo mismo para el tipo de cabello.
        $preguntaTipoCabelloObj = Pregunta::where('enunciado', '¿Cuál es tu tipo de cabello?')->first();
        $idPreguntaTipoCabello = $preguntaTipoCabelloObj ? $preguntaTipoCabelloObj->id : null;
        $tipoCabelloUsuario = null;
        $textoTipoCabelloUsuario = 'todos los tipos de cabello';

        if ($idPreguntaTipoCabello && isset($respuestasEnviadas[$idPreguntaTipoCabello])) {
            $tipoCabelloUsuario = $respuestasEnviadas[$idPreguntaTipoCabello];
            $opcionSeleccionada = $preguntaTipoCabelloObj->opciones()->where('valor_opcion', $tipoCabelloUsuario)->first();
            $textoTipoCabelloUsuario = $opcionSeleccionada ? htmlspecialchars($opcionSeleccionada->texto_opcion) : htmlspecialchars($tipoCabelloUsuario);
        }

        // Monto la query para buscar productos.
        $queryProducto = Producto::query();

        // Si eligió categoría (y no es 'todo', que sería como "cualquiera"), la añado al filtro.
        if ($categoriaProductoElegida && $categoriaProductoElegida !== 'todo') {
            $queryProducto->where('categoria', $categoriaProductoElegida);
        }

        // Si dijo tipo de cabello...
        if ($tipoCabelloUsuario) {
            $keywordParaDescripcion = "";
            // Según el tipo, busco una keyword en la descripción.
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
                    // Este es un poco especial, busco dos frases.
                    $queryProducto->where(function ($q) {
                        $q->where('descripcion', 'LIKE', '%para cabello muy rizado%')
                            ->orWhere('descripcion', 'LIKE', '%para cabello afro%');
                    });
                    $keywordParaDescripcion = null; // Ya he hecho la query, no necesito la keyword.
                    break;
            }
            // Si tengo keyword (no era el caso 'muy_rizado_afro' que ya hizo el where).
            if ($keywordParaDescripcion) {
                $queryProducto->where('descripcion', 'LIKE', '%' . $keywordParaDescripcion . '%');
            }
        }

        // Intento pillar uno al azar con los filtros que haya.
        $productoRecomendado = $queryProducto->inRandomOrder()->first();

        // PRIMER FALLBACK: Si no encontré nada y SÍ me dio tipo de cabello,
        // intento buscar OTRA VEZ solo con el tipo de cabello. Quizás la categoría era muy restrictiva.
        if (!$productoRecomendado && $tipoCabelloUsuario) {
            $queryFallbackTipoCabello = Producto::query(); // Nueva query limpia.
            // Repito la lógica del tipo de cabello. Esto podría ir a una función para no duplicar.
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
            // Aquí NO vuelvo a aplicar el filtro de categoría, solo el de tipo de cabello.
            // Si quisiera también la categoría aquí, la añadiría. Pero el original no lo hacía.
            $productoRecomendado = $queryFallbackTipoCabello->inRandomOrder()->first();
        }

        // SEGUNDO FALLBACK: Si sigo sin nada, pues uno al azar y ya está. Algo es algo.
        if (!$productoRecomendado) {
            $productoRecomendado = Producto::inRandomOrder()->first();
        }

        // Si TENGO producto para recomendar (sea como sea que lo encontré)...
        if ($productoRecomendado) {
            // Monto una justificación maja según los filtros que usé.
            $justificacionDetalle = $justificacionDetallePredeterminada; // Por defecto.
            if ($tipoCabelloUsuario && ($categoriaProductoElegida && $categoriaProductoElegida !== 'todo')) {
                $justificacionDetalle = "Basado en tu selección de cabello '{$textoTipoCabelloUsuario}' y tu interés por un '{$textoCategoriaElegida}', te sugerimos este producto.";
            } elseif ($tipoCabelloUsuario) {
                $justificacionDetalle = "Considerando tu tipo de cabello '{$textoTipoCabelloUsuario}', este producto podría interesarte.";
            } elseif ($categoriaProductoElegida && $categoriaProductoElegida !== 'todo') {
                $justificacionDetalle = "Ya que buscas un '{$textoCategoriaElegida}', te recomendamos este producto.";
            }

            // Guardo la recomendación en la BD.
            $recomendacionCreada = Recomendacion::create([
                'envio_id' => $envio->id,
                'id_producto' => $productoRecomendado->idproducto, // Asumo que el campo PK de producto es 'idproducto'
                'justificacion_titulo' => "Una sugerencia especial para ti: " . htmlspecialchars($productoRecomendado->nombre),
                'justificacion_detalle' => $justificacionDetalle,
            ]);
        }
        // --- Fin Recomendación ---

        // Redirijo a la página de gracias.
        if ($recomendacionCreada) {
            // Si hay recomendación, paso el ID y un mensaje chulo.
            return redirect()->route('cuestionarios.gracias', ['nick' => $nick, 'recomendacionId' => $recomendacionCreada->id])
                ->with('success', '¡Cuestionario enviado! ' . htmlspecialchars($nick) . ', estamos preparando tu recomendación personalizada...');
        } else {
            // Si no, pues un mensaje genérico de que todo fue bien pero sin recomendación específica.
            return redirect()->route('cuestionarios.gracias', ['nick' => $nick])
                ->with('success', '¡Cuestionario enviado con éxito, ' . htmlspecialchars($nick) . '! Aunque valoramos tus respuestas, no pudimos generar una recomendación específica esta vez.');
        }
    }


    // La página de 'gracias' y ya.
    //Le paso el nick y el ID de la recomendación si lo hay, para que la vista muestre lo que toque.

    public function mostrarPaginaGracias($nick, $recomendacionId = null)
    {
        // Muestro la vista `gracias` con el nick y el ID de recomendación (que puede ser null).
        return view('cuestionarios.gracias', ['nick' => $nick, 'recomendacionId' => $recomendacionId]);
    }
}
