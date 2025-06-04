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


    public function estrategiaSeleccionProducto(
        array $respuestasEnviadas,
        ?Pregunta $preguntaCategoriaProductoObj,
        ?int $idPreguntaCategoriaProducto,
        ?Pregunta $preguntaTipoCabelloObj,
        ?int $idPreguntaTipoCabello,
        ?Pregunta $preguntaPorosidadObj,
        ?int $idPreguntaPorosidad,
        ?Pregunta $preguntaCueroCabelludoObj,
        ?int $idPreguntaCueroCabelludo
    ): array {
        $productoRecomendado = null;
        $justificacionPartes = []; // Para construir la justificación dinámicamente

        // --- Categoría de Producto (siempre seleccionada) ---
        $categoriaProductoElegida = null;
        $textoCategoriaElegida = 'un producto';
        if ($idPreguntaCategoriaProducto && isset($respuestasEnviadas[$idPreguntaCategoriaProducto])) {
            $categoriaProductoElegida = $respuestasEnviadas[$idPreguntaCategoriaProducto];
            if ($preguntaCategoriaProductoObj) {
                $opcionSeleccionada = $preguntaCategoriaProductoObj->opciones()->where('valor_opcion', $categoriaProductoElegida)->first(); //
                $textoCategoriaElegida = $opcionSeleccionada ? htmlspecialchars($opcionSeleccionada->texto_opcion) : htmlspecialchars($categoriaProductoElegida); //
            } else {
                $textoCategoriaElegida = htmlspecialchars($categoriaProductoElegida);
            }
        } else {
            return ['producto' => null, 'justificacion' => 'No se pudo determinar la categoría de producto deseada.'];
        }

        // --- Tipo de Cabello ---
        $tipoCabelloUsuario = null;
        $textoTipoCabelloUsuario = '';
        if ($idPreguntaTipoCabello && isset($respuestasEnviadas[$idPreguntaTipoCabello])) {
            $tipoCabelloUsuario = $respuestasEnviadas[$idPreguntaTipoCabello];
            if ($preguntaTipoCabelloObj) {
                $opcionSeleccionada = $preguntaTipoCabelloObj->opciones()->where('valor_opcion', $tipoCabelloUsuario)->first(); //
                $textoTipoCabelloUsuario = $opcionSeleccionada ? htmlspecialchars($opcionSeleccionada->texto_opcion) : htmlspecialchars($tipoCabelloUsuario); //
            } else {
                $textoTipoCabelloUsuario = htmlspecialchars($tipoCabelloUsuario);
            }
        }

        // --- Porosidad del Cabello ---
        $porosidadUsuario = null;
        $textoPorosidadUsuario = '';
        if ($idPreguntaPorosidad && isset($respuestasEnviadas[$idPreguntaPorosidad])) {
            $porosidadUsuario = $respuestasEnviadas[$idPreguntaPorosidad];
            if ($preguntaPorosidadObj) {
                $opcionSeleccionada = $preguntaPorosidadObj->opciones()->where('valor_opcion', $porosidadUsuario)->first(); //
                $textoPorosidadUsuario = $opcionSeleccionada ? htmlspecialchars($opcionSeleccionada->texto_opcion) : htmlspecialchars($porosidadUsuario); //
            } else {
                $textoPorosidadUsuario = htmlspecialchars($porosidadUsuario);
            }
        }

        // --- Tipo de Cuero Cabelludo ---
        $cueroCabelludoUsuario = null;
        $textoCueroCabelludoUsuario = '';
        if ($idPreguntaCueroCabelludo && isset($respuestasEnviadas[$idPreguntaCueroCabelludo])) {
            $cueroCabelludoUsuario = $respuestasEnviadas[$idPreguntaCueroCabelludo];
            if ($preguntaCueroCabelludoObj) {
                $opcionSeleccionada = $preguntaCueroCabelludoObj->opciones()->where('valor_opcion', $cueroCabelludoUsuario)->first(); //
                $textoCueroCabelludoUsuario = $opcionSeleccionada ? htmlspecialchars($opcionSeleccionada->texto_opcion) : htmlspecialchars($cueroCabelludoUsuario); //
            } else {
                $textoCueroCabelludoUsuario = htmlspecialchars($cueroCabelludoUsuario);
            }
        }

        // --- Lógica de Selección de Producto ---

        // Intento 1: Categoría + Tipo Cabello + Porosidad + Cuero Cabelludo
        // Solo si se han proporcionado todas estas respuestas.
        if ($tipoCabelloUsuario && $porosidadUsuario && $cueroCabelludoUsuario) {
            $queryAttempt1 = Producto::query()->where('categoria', $categoriaProductoElegida); //
            $attempt1JustificationParts = ["tu interés por un '" . $textoCategoriaElegida . "'"];

            // Aplicar filtro Tipo Cabello
            $keywordTipoCabello1 = "";
            switch ($tipoCabelloUsuario) {
                case 'liso':
                    $keywordTipoCabello1 = "para cabello liso";
                    break;
                case 'ondulado':
                    $keywordTipoCabello1 = "para cabello ondulado";
                    break;
                case 'rizado':
                    $keywordTipoCabello1 = "para cabello rizado";
                    break;
                case 'muy_rizado_afro':
                    $queryAttempt1->where(function ($q) {
                        $q->where('descripcion', 'LIKE', '%para cabello muy rizado%')
                            ->orWhere('descripcion', 'LIKE', '%para cabello afro%');
                    });
                    $keywordTipoCabello1 = null;
                    break;
            }
            if ($keywordTipoCabello1) {
                $queryAttempt1->where('descripcion', 'LIKE', '%' . $keywordTipoCabello1 . '%');
            }
            $attempt1JustificationParts[] = "tu tipo de cabello '" . $textoTipoCabelloUsuario . "'";

            // Aplicar filtro Porosidad
            $keywordPorosidad1 = "";
            switch ($porosidadUsuario) {
                case 'baja':
                    $keywordPorosidad1 = "baja porosidad";
                    break;
                case 'media':
                    $keywordPorosidad1 = "media porosidad";
                    break;
                case 'alta':
                    $keywordPorosidad1 = "alta porosidad";
                    break;
            }
            if ($keywordPorosidad1) {
                $queryAttempt1->where('descripcion', 'LIKE', '%' . $keywordPorosidad1 . '%');
            }
            $attempt1JustificationParts[] = "la porosidad '" . $textoPorosidadUsuario . "'";

            // Aplicar filtro Cuero Cabelludo
            $keywordCueroCabelludo1 = "";
            switch ($cueroCabelludoUsuario) {
                case 'seco':
                    $keywordCueroCabelludo1 = "cuero cabelludo seco";
                    break;
                case 'normal':
                    $keywordCueroCabelludo1 = "cuero cabelludo normal";
                    break;
                case 'graso':
                    $keywordCueroCabelludo1 = "cuero cabelludo graso";
                    break;
                case 'sensible':
                    $keywordCueroCabelludo1 = "cuero cabelludo sensible";
                    break;
                case 'con_caspa':
                    $keywordCueroCabelludo1 = "anticaspa";
                    break;
            }
            if ($keywordCueroCabelludo1) {
                $queryAttempt1->where('descripcion', 'LIKE', '%' . $keywordCueroCabelludo1 . '%');
            }
            $attempt1JustificationParts[] = "tu tipo de cuero cabelludo '" . $textoCueroCabelludoUsuario . "'";

            $productoRecomendado = $queryAttempt1->inRandomOrder()->first();
            if ($productoRecomendado) {
                $justificacionPartes = $attempt1JustificationParts;
            }
        }

        // Intento 2 (Fallback): Categoría + Tipo de Cabello
        // Se ejecuta si el Intento 1 no encontró producto O si no se proporcionaron todas las respuestas para el Intento 1,
        // pero SÍ se proporcionó el tipo de cabello.
        if (!$productoRecomendado && $tipoCabelloUsuario) {
            $queryAttempt2 = Producto::query()->where('categoria', $categoriaProductoElegida); //
            $attempt2JustificationParts = ["tu interés por un '" . $textoCategoriaElegida . "'"];

            $keywordTipoCabello2 = "";
            switch ($tipoCabelloUsuario) {
                case 'liso':
                    $keywordTipoCabello2 = "para cabello liso";
                    break;
                case 'ondulado':
                    $keywordTipoCabello2 = "para cabello ondulado";
                    break;
                case 'rizado':
                    $keywordTipoCabello2 = "para cabello rizado";
                    break;
                case 'muy_rizado_afro':
                    $queryAttempt2->where(function ($q) {
                        $q->where('descripcion', 'LIKE', '%para cabello muy rizado%')
                            ->orWhere('descripcion', 'LIKE', '%para cabello afro%');
                    });
                    $keywordTipoCabello2 = null;
                    break;
            }
            if ($keywordTipoCabello2) {
                $queryAttempt2->where('descripcion', 'LIKE', '%' . $keywordTipoCabello2 . '%');
            }
            $attempt2JustificationParts[] = "tu tipo de cabello '" . $textoTipoCabelloUsuario . "'";

            $productoRecomendado = $queryAttempt2->inRandomOrder()->first();
            if ($productoRecomendado) {
                $justificacionPartes = $attempt2JustificationParts;
            }
        }

        // Si después de estos dos intentos no hay producto, $productoRecomendado sigue siendo null.

        // --- Construcción de la Justificación Final ---
        $justificacionDetalle = "No hemos podido encontrar un producto que se ajuste perfectamente a todos tus criterios en este momento."; // Default si no hay producto
        if ($productoRecomendado && !empty($justificacionPartes)) {
            $justificacionDetalle = "Considerando " . implode(" y ", $justificacionPartes) . ", te sugerimos este producto.";
        } elseif ($productoRecomendado) { // Si hay producto pero $justificacionPartes está vacía (debería ser raro con la lógica actual)
            $justificacionDetalle = "Te sugerimos este '" . $textoCategoriaElegida . "' que podría interesarte, aunque no hemos podido detallar más la selección basada en todas tus respuestas.";
        }

        return ['producto' => $productoRecomendado, 'justificacion' => $justificacionDetalle];
    }

    public function procesarEnvioParaNick(Request $request, $nick, $id_cuestionario)
    {
        $cuestionario = Cuestionario::findOrFail($id_cuestionario); //
        $preguntasDelCuestionario = $cuestionario->preguntas()->with('opciones')->get(); //

        $rules = [];
        $messages = [];
        foreach ($preguntasDelCuestionario as $pregunta) { //
            if ($pregunta->tipo_input === 'checkbox') { //
                $rules['respuestas.' . $pregunta->id] = 'nullable|array'; //
            }
            // Asegurar que la pregunta de categoría de producto es obligatoria
            if ($pregunta->enunciado === '¿Qué producto quieres que te recomendemos?') { //
                $rules['respuestas.' . $pregunta->id] = 'required'; //
                $messages['respuestas.' . $pregunta->id . '.required'] = 'Debes seleccionar qué tipo de producto te recomendamos.'; //
            } else {
                // Para otras preguntas, decide si son 'required' o 'nullable'
                // Por ahora, mantendremos 'required' como en tu código anterior.
                $rules['respuestas.' . $pregunta->id] = 'required'; //
                $messages['respuestas.' . $pregunta->id . '.required'] = 'La pregunta "' . htmlspecialchars(Str::limit($pregunta->enunciado, 50)) . '" es obligatoria.'; //
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->route('cuestionarios.mostrarParaNick', [
                'nick' => $nick,
                'id_cuestionario' => $id_cuestionario,
            ])
                ->withErrors($validator)
                ->withInput();
        }

        $usuario = Usuario::where('Nombre', $nick)->first(); //
        $usuarioCodigo = $usuario ? $usuario->Codigo : null; //

        $envio = CuestionarioEnvio::create([ //
            'usuario_codigo' => $usuarioCodigo,
            'cuestionario_id' => $id_cuestionario,
            'nick_utilizado' => $nick,
        ]);

        $respuestasEnviadas = $request->input('respuestas', []);

        foreach ($respuestasEnviadas as $id_pregunta => $valor_respuesta_o_array) {
            if (is_array($valor_respuesta_o_array)) {
                foreach ($valor_respuesta_o_array as $valor_individual) {
                    Respuesta::create([ //
                        'envio_id' => $envio->id,
                        'id_preguntas' => $id_pregunta,
                        'valor_pregunta' => $valor_individual,
                    ]);
                }
            } else {
                Respuesta::create([ //
                    'envio_id' => $envio->id,
                    'id_preguntas' => $id_pregunta,
                    'valor_pregunta' => $valor_respuesta_o_array,
                ]);
            }
        }

        $preguntaCategoriaProductoObj = Pregunta::where('enunciado', '¿Qué producto quieres que te recomendemos?')->first(); //
        $idPreguntaCategoriaProducto = $preguntaCategoriaProductoObj ? $preguntaCategoriaProductoObj->id : null; //

        if (!$idPreguntaCategoriaProducto || !isset($respuestasEnviadas[$idPreguntaCategoriaProducto])) {
            return redirect()->route('cuestionarios.mostrarParaNick', [
                'nick' => $nick,
                'id_cuestionario' => $id_cuestionario,
            ])
                ->with('error', 'Hubo un problema al procesar la categoría del producto. Por favor, inténtalo de nuevo.')
                ->withInput();
        }

        $preguntaTipoCabelloObj = Pregunta::where('enunciado', '¿Cuál es tu tipo de cabello?')->first(); //
        $idPreguntaTipoCabello = $preguntaTipoCabelloObj ? $preguntaTipoCabelloObj->id : null; //

        $preguntaPorosidadObj = Pregunta::where('enunciado', '¿Cuál es la porosidad de tu cabello? (Haz la prueba de flotación: coloca un cabello limpio en un vaso de agua. Si flota, es de baja porosidad; si se hunde rápido, es de alta porosidad.)')->first(); //
        $idPreguntaPorosidad = $preguntaPorosidadObj ? $preguntaPorosidadObj->id : null; //

        $preguntaCueroCabelludoObj = Pregunta::where('enunciado', '¿Cómo describirías tu cuero cabelludo?')->first(); //
        $idPreguntaCueroCabelludo = $preguntaCueroCabelludoObj ? $preguntaCueroCabelludoObj->id : null; //

        $seleccion = $this->estrategiaSeleccionProducto(
            $respuestasEnviadas,
            $preguntaCategoriaProductoObj,
            $idPreguntaCategoriaProducto,
            $preguntaTipoCabelloObj,
            $idPreguntaTipoCabello,
            $preguntaPorosidadObj,
            $idPreguntaPorosidad,
            $preguntaCueroCabelludoObj,
            $idPreguntaCueroCabelludo
        );

        $productoRecomendado = $seleccion['producto'];
        $justificacionDetalle = $seleccion['justificacion'];
        $recomendacionCreada = null;

        if ($productoRecomendado) {
            $recomendacionCreada = Recomendacion::create([ //
                'envio_id' => $envio->id,
                'id_producto' => $productoRecomendado->idproducto, //
                'justificacion_titulo' => "Una sugerencia especial para ti: " . htmlspecialchars($productoRecomendado->nombre), //
                'justificacion_detalle' => $justificacionDetalle,
            ]);
        }


        if ($recomendacionCreada) {
            return redirect()->route('cuestionarios.gracias', [
                'nick' => $nick,
                'recomendacionId' => $recomendacionCreada->id
            ])
                ->with('success', '¡Cuestionario enviado! ' . htmlspecialchars($nick) . ', estamos preparando tu recomendación personalizada...')
                ->with('id_cuestionario_procesado', $id_cuestionario); // <--- LÍNEA AÑADIDA/MODIFICADA
        } else {
            // Mensaje cuando no se encuentra un producto suficientemente específico
            $mensajeNoEncontrado = '¡Gracias por tus respuestas, ' . htmlspecialchars($nick) . '! De momento, no hemos encontrado un producto que se ajuste perfectamente a tus selecciones. Por favor, considera ajustar tus criterios o explora nuestra gama general de productos.';
            return redirect()->route('cuestionarios.gracias', [
                'nick' => $nick
                // recomendacionId es omitido aquí, así que será null en el controlador de la vista 'gracias'
            ])
                ->with('success', $mensajeNoEncontrado)
                ->with('id_cuestionario_procesado', $id_cuestionario); // <--- LÍNEA AÑADIDA/MODIFICADA
        }
    }



    // La página de 'gracias' y ya.
    //Le paso el nick y el ID de la recomendación si lo hay, para que la vista muestre lo que toque.

    public function mostrarPaginaGracias($nick, $recomendacionId = null)
    {
        // Muestro la vista `gracias` con el nick y el ID de recomendación (que puede ser null).
        return view('cuestionarios.gracias', [
            'nick' => $nick,
            'recomendacionId' => $recomendacionId,
            'id_cuestionario_actual' => session('id_cuestionario_procesado')
        ]); //
    }
}
