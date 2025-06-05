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
use Illuminate\Support\Facades\Validator;

class CuestionarioController extends Controller
{

    public function listar()
    {
        // Consulto la base de datos para traer los cuestionarios activos, ordenados por título.
        $cuestionarios = Cuestionario::where('estado', 'ACTIVO')->orderBy('titulo')->get();
        // Le paso estos cuestionarios a la vista 'cuestionarios.listar' para que los muestre.
        return view('cuestionarios.listar', ['cuestionarios' => $cuestionarios]);
    }


    public function mostrarParaNick(Request $request, $nick, $id_cuestionario)
    {
        // Busco el cuestionario por su ID y me aseguro de que esté ACTIVO.
        // Si no lo encuentra o no está activo, firstOrFail() automáticamente mostrará un error 404.
        $cuestionario = Cuestionario::where('id', $id_cuestionario)->where('estado', 'ACTIVO')->firstOrFail();

        // Cargo todas las preguntas asociadas a este cuestionario.
        // El with('opciones') es una optimización para cargar las opciones de cada pregunta
        // en la misma consulta y evitar múltiples accesos a la base de datos después (evitar N+1).
        $preguntas = $cuestionario->preguntas()->with('opciones')->get();

        // Identifico la pregunta especial "¿Qué producto quieres que te recomendemos?"
        // Guardo su ID para poder tratarla de forma diferente, por ejemplo, para no rellenarla
        // automáticamente si se está cargando un envío previo, porque quiero que el usuario la elija siempre.
        $preguntaFiltroObj = Pregunta::where('enunciado', '¿Qué producto quieres que te recomendemos?')->first();
        $idPreguntaFiltroAExcluir = $preguntaFiltroObj ? $preguntaFiltroObj->id : null;

        // Preparo un array para guardar las respuestas previas, si es que hay que cargarlas.
        $respuestasPreviasFormateadas = [];
        // Compruebo si en la URL me llegó un parámetro 'envio_previo'.
        $envioPrevioId = $request->query('envio_previo');

        // Si tengo un ID de envío previo...
        if ($envioPrevioId) {
            // ...cargo ese envío, junto con sus respuestas y la pregunta a la que pertenece cada respuesta.
            $envioPrevio = CuestionarioEnvio::with('respuestas.pregunta')->find($envioPrevioId);
            // Me aseguro de que el envío exista y sea realmente de este cuestionario.
            if ($envioPrevio && $envioPrevio->cuestionario_id == $id_cuestionario) {
                // Si todo está bien, recorro cada respuesta guardada.
                foreach ($envioPrevio->respuestas as $respuesta) {
                    $preguntaId = $respuesta->id_preguntas;
                    $valorRespuesta = $respuesta->valor_pregunta;
                    // Necesito saber el tipo de input (radio, checkbox, etc.) para formatear la respuesta correctamente.
                    $tipoInput = $respuesta->pregunta ? $respuesta->pregunta->tipo_input : null;

                    // Si es un checkbox, puede tener múltiples valores, así que lo guardo como un array.
                    if ($tipoInput === 'checkbox') {
                        if (!isset($respuestasPreviasFormateadas[$preguntaId])) {
                            $respuestasPreviasFormateadas[$preguntaId] = [];
                        }
                        $respuestasPreviasFormateadas[$preguntaId][] = $valorRespuesta;
                    } else {
                        // Para los demás tipos, es un solo valor.
                        $respuestasPreviasFormateadas[$preguntaId] = $valorRespuesta;
                    }
                }
            }
        }

        // Finalmente, muestro la vista 'cuestionarios.ver_para_nick' y le paso todos los datos que necesita.
        return view('cuestionarios.ver_para_nick', [
            'nick' => $nick,
            'cuestionario' => $cuestionario,
            'preguntas' => $preguntas,
            'previousAnswers' => $respuestasPreviasFormateadas,
            'idPreguntaFiltro' => $idPreguntaFiltroAExcluir,
        ]);
    }


    public function nuevaEstrategiaSeleccionProducto(
        string $categoriaProductoElegida,
        array $criteriosUsuario,
        int $umbralPorcentaje = 40
    ): array { // Devuelvo un array con el producto y la justificación.

        // Inicializo las variables que voy a usar.
        $productoRecomendado = null;
        $mejorPuntuacionAdicional = -1;
        // Mensaje por defecto si no encuentro un producto que cumpla los filtros principales.
        $justificacionFinal = "No se encontró un producto que coincida con tu categoría ('" . $categoriaProductoElegida . "') y tipo de cabello especificados.";
        $palabrasClaveCoincidentesParaJustificacion = []; // Para construir un mensaje de justificación claro.

        //  Separar el criterio de "Tipo de Cabello" del resto 
        // Necesito el tipo de cabello como un filtro principal, y los demás criterios para la puntuación.
        $palabraClaveTipoCabello = null;
        $textoTipoCabelloParaJustificacion = "";
        $palabrasClaveAdicionalesUsuario = [];

        foreach ($criteriosUsuario as $criterio) {
            // Comparo el enunciado para encontrar la respuesta a "¿Cuál es tu tipo de cabello?".
            if (isset($criterio['pregunta_enunciado']) && $criterio['pregunta_enunciado'] === '¿Cuál es tu tipo de cabello?') {
                $palabraClaveTipoCabello = strtolower(trim($criterio['valor_opcion']));
                $textoTipoCabelloParaJustificacion = $criterio['texto_opcion'];
            } else {
                // Si no es la pregunta del tipo de cabello, lo guardo como criterio adicional.
                // Me aseguro de que el criterio tenga toda la información que necesito.
                if (isset($criterio['valor_opcion']) && isset($criterio['texto_opcion']) && isset($criterio['pregunta_enunciado'])) {
                    $palabrasClaveAdicionalesUsuario[] = [
                        'pregunta_enunciado' => $criterio['pregunta_enunciado'],
                        'valor_opcion' => strtolower(trim($criterio['valor_opcion'])),
                        'texto_opcion' => $criterio['texto_opcion']
                    ];
                }
            }
        }

        // Si el usuario no especificó su tipo de cabello (aunque debería ser obligatorio en el formulario), no puedo continuar.
        if (!$palabraClaveTipoCabello) {
            return ['producto' => null, 'justificacion' => "Es necesario que especifiques tu tipo de cabello para una recomendación adecuada."];
        }

        // FILTRO OBLIGATORIO por CATEGORÍA DE PRODUCTO 
        // Busco todos los productos que pertenezcan a la categoría que el usuario eligió.
        $productosPorCategoria = Producto::where('categoria', $categoriaProductoElegida)->get();

        // Si no hay productos en esa categoría en mi base de datos, no hay nada que recomendar.
        if ($productosPorCategoria->isEmpty()) {
            return ['producto' => null, 'justificacion' => "No hay productos disponibles en la categoría '" . $categoriaProductoElegida . "'."];
        }

        // FILTRO OBLIGATORIO por TIPO DE CABELLO 
        $productosFiltradosPorTipoCabello = []; // Aquí guardaré los productos que pasen este filtro.
        // Preparo los términos de búsqueda para el tipo de cabello.
        // Busco tanto la palabra clave directa (ej. "liso") como una frase más completa (ej. "cabello liso").
        $terminosBusquedaTipoCabello = [$palabraClaveTipoCabello];
        switch ($palabraClaveTipoCabello) {
            case 'liso':
                $terminosBusquedaTipoCabello[] = "cabello liso";
                break;
            case 'ondulado':
                $terminosBusquedaTipoCabello[] = "cabello ondulado";
                break;
            case 'rizado':
                $terminosBusquedaTipoCabello[] = "cabello rizado";
                break;
            case 'muy_rizado_afro': // Este valor_opcion es de mi BBDD
                $terminosBusquedaTipoCabello[] = "cabello muy rizado";
                $terminosBusquedaTipoCabello[] = "cabello afro";
                $terminosBusquedaTipoCabello[] = "muy rizado / afro";
                break;
        }
        $terminosBusquedaTipoCabello = array_unique($terminosBusquedaTipoCabello); // Elimino términos duplicados.

        // Recorro los productos de la categoría y los filtro por tipo de cabello.
        foreach ($productosPorCategoria as $producto) {
            // Combino nombre y descripción del producto para tener más texto donde buscar. Lo paso a minúsculas.
            $textoProductoAComparar = strtolower(($producto->nombre ?? '') . " " . ($producto->descripcion ?? ''));
            foreach ($terminosBusquedaTipoCabello as $terminoTipoCabello) {
                // Si alguno de los términos de búsqueda para el tipo de cabello está en el texto del producto...
                if (str_contains($textoProductoAComparar, $terminoTipoCabello)) {
                    $productosFiltradosPorTipoCabello[] = $producto; // ...lo añado a mi lista de candidatos.
                    break; // No necesito seguir buscando más términos para este producto.
                }
            }
        }

        // Si ningún producto coincide con la categoría Y el tipo de cabello, no puedo recomendar.
        if (empty($productosFiltradosPorTipoCabello)) {
            $justificacionFinal = "No encontramos productos para la categoría '" . $categoriaProductoElegida . "' que coincidan específicamente con tu tipo de cabello ('" . $textoTipoCabelloParaJustificacion . "').";
            return ['producto' => null, 'justificacion' => $justificacionFinal];
        }

        // Si el usuario SOLO especificó categoría y tipo de cabello,
        // y no hay más criterios adicionales para puntuar.
        if (empty($palabrasClaveAdicionalesUsuario)) {
            $productoRecomendado = $productosFiltradosPorTipoCabello[0];
            $justificacionFinal = "Para la categoría '" . $categoriaProductoElegida . "' y tu tipo de cabello ('" . $textoTipoCabelloParaJustificacion . "'), te sugerimos '" . $productoRecomendado->nombre . "'.";
            return [
                'producto' => $productoRecomendado,
                'justificacion' => $justificacionFinal
            ];
        }


        // Ahora, sobre los productos que ya cumplen categoría y tipo de cabello,
        // aplico la puntuación basada en los demás criterios del usuario.
        foreach ($productosFiltradosPorTipoCabello as $producto) {
            $textoProductoAComparar = strtolower(($producto->nombre ?? '') . " " . ($producto->descripcion ?? ''));
            $coincidenciasAdicionales = 0;
            $palabrasAdicionalesQueCoincidieronEnEsteProducto = [];

            // Recorro cada criterio adicional que el usuario seleccionó.
            foreach ($palabrasClaveAdicionalesUsuario as $criterioAdicionalCompleto) {
                $keywordAdicional = $criterioAdicionalCompleto['valor_opcion'];
                $enunciadoPreguntaAdicional = $criterioAdicionalCompleto['pregunta_enunciado'];
                $terminosDeBusquedaAdicional = [$keywordAdicional];

                // Aquí es donde hago "más inteligente" la búsqueda.
                // Mapeo los 'valor_opcion' a frases que probablemente estén en las descripciones.
                // Uso el 'pregunta_enunciado' para diferenciar palabras clave que podrían ser ambiguas (ej. "seco").
                if ($keywordAdicional === 'baja' || $keywordAdicional === 'media' || $keywordAdicional === 'alta') { //
                    $terminosDeBusquedaAdicional[] = $keywordAdicional . " porosidad";
                    $terminosDeBusquedaAdicional[] = "porosidad " . $keywordAdicional;
                } elseif ($keywordAdicional === 'frizz_encrespamiento') { //
                    $terminosDeBusquedaAdicional[] = "frizz";
                    $terminosDeBusquedaAdicional[] = "encrespamiento";
                    $terminosDeBusquedaAdicional[] = "anti-frizz";
                } elseif ($keywordAdicional === 'puntas_abiertas') { //
                    $terminosDeBusquedaAdicional[] = "puntas abiertas";
                    $terminosDeBusquedaAdicional[] = "reparar puntas";
                    $terminosDeBusquedaAdicional[] = "sellar puntas";
                } elseif ($keywordAdicional === 'seco' && $enunciadoPreguntaAdicional === '¿Cómo describirías tu cuero cabelludo?') {
                    $terminosDeBusquedaAdicional[] = "cuero cabelludo seco";
                } elseif ($keywordAdicional === 'graso' && $enunciadoPreguntaAdicional === '¿Cómo describirías tu cuero cabelludo?') {
                    $terminosDeBusquedaAdicional[] = "cuero cabelludo graso";
                } elseif ($keywordAdicional === 'sensible' && $enunciadoPreguntaAdicional === '¿Cómo describirías tu cuero cabelludo?') {
                    $terminosDeBusquedaAdicional[] = "cuero cabelludo sensible";
                } elseif ($keywordAdicional === 'con_caspa' && $enunciadoPreguntaAdicional === '¿Cómo describirías tu cuero cabelludo?') {
                    $terminosDeBusquedaAdicional[] = "anticaspa";
                    $terminosDeBusquedaAdicional[] = "anti-caspa";
                    $terminosDeBusquedaAdicional[] = "caspa";
                } elseif ($keywordAdicional === 'falta_brillo') { //
                    $terminosDeBusquedaAdicional[] = "brillo";
                    $terminosDeBusquedaAdicional[] = "falta de brillo";
                    $terminosDeBusquedaAdicional[] = "cabello opaco";
                } elseif ($keywordAdicional === 'falta_volumen') { //
                    $terminosDeBusquedaAdicional[] = "volumen";
                    $terminosDeBusquedaAdicional[] = "falta de volumen";
                    $terminosDeBusquedaAdicional[] = "dar cuerpo";
                }
                // Es importante seguir añadiendo mapeos para todos los 'valor_opcion' importantes
                // de mis preguntas (caida_excesiva, cabello_quebradizo, etc.).

                $terminosDeBusquedaAdicional = array_unique($terminosDeBusquedaAdicional);

                // Busco si alguno de estos términos mapeados está en la descripción del producto.
                foreach ($terminosDeBusquedaAdicional as $terminoAdicional) {
                    if (str_contains($textoProductoAComparar, $terminoAdicional)) {
                        $coincidenciasAdicionales++;
                        $palabrasAdicionalesQueCoincidieronEnEsteProducto[] = $criterioAdicionalCompleto['texto_opcion'];
                        break; // Este criterio coincidió, no necesito buscar más para este criterio.
                    }
                }
            }

            // Calculo el porcentaje de coincidencia para los criterios adicionales.
            $porcentajeCoincidenciaAdicional = 0;
            if (count($palabrasClaveAdicionalesUsuario) > 0) {
                $porcentajeCoincidenciaAdicional = ($coincidenciasAdicionales / count($palabrasClaveAdicionalesUsuario)) * 100;
            }

            // Si este producto tiene una puntuación que supera el umbral Y es mejor que la
            // mejor puntuación que había encontrado hasta ahora, lo actualizo como mi candidato.
            if ($porcentajeCoincidenciaAdicional >= $umbralPorcentaje && $porcentajeCoincidenciaAdicional > $mejorPuntuacionAdicional) {
                $mejorPuntuacionAdicional = $porcentajeCoincidenciaAdicional;
                $productoRecomendado = $producto;
                $palabrasClaveCoincidentesParaJustificacion = array_unique($palabrasAdicionalesQueCoincidieronEnEsteProducto);
            }
        }

        //  Construir la justificación final y devolver el resultado 
        if ($productoRecomendado) { // Si encontré un producto que superó el umbral.
            $justificacionFinal = "Considerando la categoría '" . $categoriaProductoElegida . "' y tu tipo de cabello ('" . $textoTipoCabelloParaJustificacion . "'), y ";
            if (!empty($palabrasClaveCoincidentesParaJustificacion)) {
                $justificacionFinal .= "tus preferencias por: " . implode(", ", array_map('htmlspecialchars', $palabrasClaveCoincidentesParaJustificacion));
                $justificacionFinal .= " (coincidencia de estos criterios adicionales: " . round($mejorPuntuacionAdicional) . "%), ";
            } else if ($mejorPuntuacionAdicional >= 0 && !empty($palabrasClaveAdicionalesUsuario)) {
                // Había criterios adicionales, pero el producto recomendado no coincidió con ninguno de ellos (o su puntuación fue 0),
                // pero aun así fue el "mejor" (quizás el único que pasó los filtros principales).
                $justificacionFinal .= "aunque no encontramos una alta coincidencia con otros criterios específicos (0% de coincidencia adicional relevante), ";
            } else {
                // No había criterios adicionales para puntuar, así que la recomendación se basa solo en categoría y tipo de cabello.
                $justificacionFinal .= "basándonos en estos filtros principales, ";
            }
            $justificacionFinal .= "te recomendamos '" . $productoRecomendado->nombre . "'.";
        } else {
            // FALLBACK: Si no encontré ningún producto que superara el umbral de los criterios adicionales,
            // pero SÍ tenía productos que cumplían categoría y tipo de cabello, recomiendo el primero de ellos.
            if (!empty($productosFiltradosPorTipoCabello)) {
                $productoRecomendado = $productosFiltradosPorTipoCabello[0];
                $justificacionFinal = "Para la categoría '" . $categoriaProductoElegida . "' y tu tipo de cabello ('" . $textoTipoCabelloParaJustificacion . "'), una opción general es '" . $productoRecomendado->nombre . "'. No pudimos encontrar una coincidencia alta con todos tus otros criterios específicos, pero este producto se ajusta a lo principal.";
            }
            // Si $productosFiltradosPorTipoCabello estaba vacío, la $justificacionFinal ya tiene el mensaje de error adecuado del Paso 3.
        }

        return ['producto' => $productoRecomendado, 'justificacion' => $justificacionFinal];
    }

    // Este método se llama cuando el usuario envía el formulario del cuestionario.*/
    public function procesarEnvioParaNick(Request $request, $nick, $id_cuestionario)
    {
        // Cargo el cuestionario y todas sus preguntas con sus opciones.
        $cuestionario = Cuestionario::findOrFail($id_cuestionario);
        $preguntasDelCuestionario = $cuestionario->preguntas()->with('opciones')->get();

        // Defino las reglas de validación para las respuestas.
        $rules = [];
        $messages = [];
        foreach ($preguntasDelCuestionario as $pregunta) {
            if ($pregunta->tipo_input === 'checkbox') {
                $rules['respuestas.' . $pregunta->id] = 'nullable|array';
                $rules['respuestas.' . $pregunta->id . '.*'] = 'string';
            } elseif ($pregunta->enunciado === '¿Qué producto quieres que te recomendemos?') {
                $rules['respuestas.' . $pregunta->id] = 'required|string';
                $messages['respuestas.' . $pregunta->id . '.required'] = 'Debes seleccionar qué tipo de producto te recomendamos.';
            } elseif ($pregunta->enunciado === '¿Cuál es tu tipo de cabello?') {
                $rules['respuestas.' . $pregunta->id] = 'required|string';
                $messages['respuestas.' . $pregunta->id . '.required'] = 'Debes seleccionar tu tipo de cabello.';
            } else {
                // Las demás preguntas las marco como 'nullable', lo que significa que no son
                // estrictamente obligatorias en el backend. Mi estrategia puede funcionar con menos datos.
                // Si quisiera que fueran obligatorias, las pondría como 'required|string'.
                $rules['respuestas.' . $pregunta->id] = 'nullable|string';
            }
        }

        // Ejecuto el validador de Laravel.
        $validator = Validator::make($request->all(), $rules, $messages);

        // Si la validación falla
        if ($validator->fails()) {
            // redirijo al usuario de vuelta al formulario del cuestionario,
            // mostrando los errores y manteniendo los datos que ya había ingresado.
            // También paso 'scrollToError' para que, si tengo JS en la vista, pueda hacer scroll al primer error.
            return redirect()->route('cuestionarios.mostrarParaNick', [
                'nick' => $nick,
                'id_cuestionario' => $id_cuestionario,
            ])
                ->withErrors($validator)
                ->withInput()
                ->with('scrollToError', true);
        }

        // Si la validación pasa, busco al usuario y creo un nuevo registro de envío del cuestionario.
        $usuario = Usuario::where('Nombre', $nick)->first();
        $usuarioCodigo = $usuario ? $usuario->Codigo : null;

        $envio = CuestionarioEnvio::create([
            'usuario_codigo' => $usuarioCodigo,
            'cuestionario_id' => $id_cuestionario,
            'nick_utilizado' => $nick,
        ]);

        // Obtengo todas las respuestas enviadas por el usuario.
        $respuestasEnviadas = $request->input('respuestas', []);

        // Guardo cada respuesta en la base de datos.
        foreach ($respuestasEnviadas as $id_pregunta_db => $valor_respuesta_db) {
            if (is_array($valor_respuesta_db)) {
                if (!empty($valor_respuesta_db)) {
                    foreach ($valor_respuesta_db as $valor_individual_db) {
                        if ($valor_individual_db !== null) {
                            Respuesta::create([
                                'envio_id' => $envio->id,
                                'id_preguntas' => $id_pregunta_db,
                                'valor_pregunta' => $valor_individual_db,
                            ]);
                        }
                    }
                }
            } else { // Si es radio, text, etc. (un solo valor).
                if ($valor_respuesta_db !== null) {
                    Respuesta::create([
                        'envio_id' => $envio->id,
                        'id_preguntas' => $id_pregunta_db,
                        'valor_pregunta' => $valor_respuesta_db,
                    ]);
                }
            }
        }

        // Obtengo el ID de la pregunta de categoría de producto y la respuesta del usuario.
        $preguntaCategoriaProductoObj = $preguntasDelCuestionario->firstWhere('enunciado', '¿Qué producto quieres que te recomendemos?');
        $idPreguntaCategoriaProducto = $preguntaCategoriaProductoObj ? $preguntaCategoriaProductoObj->id : null;

        // El validador ya se aseguró de que esta respuesta existe y es un string.
        $categoriaProductoElegida = $respuestasEnviadas[$idPreguntaCategoriaProducto] ?? null;

        // Doble chequeo por si acaso, aunque la validación debería cubrir esto.
        if (empty($categoriaProductoElegida)) {
            return redirect()->route('cuestionarios.mostrarParaNick', [
                'nick' => $nick,
                'id_cuestionario' => $id_cuestionario,
            ])
                ->with('error', 'La categoría del producto es inválida o no fue proporcionada.')
                ->withInput();
        }

        // Construyo el array $criteriosUsuario con las demás respuestas.
        // Este array es el que pasaré a mi función de estrategia.
        $criteriosUsuario = [];
        foreach ($respuestasEnviadas as $id_pregunta_iter => $valor_respuesta_iter) {
            // No incluyo la pregunta de categoría de producto aquí, ya la tengo.
            if ($id_pregunta_iter == $idPreguntaCategoriaProducto) {
                continue;
            }

            // Si la respuesta está vacía o es nula, la omito.
            if ($valor_respuesta_iter === null || $valor_respuesta_iter === '' || (is_array($valor_respuesta_iter) && empty(array_filter($valor_respuesta_iter)))) {
                continue;
            }

            // Busco el objeto Pregunta correspondiente a esta respuesta.
            $preguntaObj = $preguntasDelCuestionario->firstWhere('id', $id_pregunta_iter);
            if (!$preguntaObj) continue; // Si no encuentro la pregunta, la omito.

            // Si la respuesta es un array (checkbox).
            if (is_array($valor_respuesta_iter)) {
                foreach ($valor_respuesta_iter as $val) {
                    if ($val === null || $val === '') continue; // Omito valores vacíos dentro del array.
                    $opcion = $preguntaObj->opciones->firstWhere('valor_opcion', $val); // Busco la opción seleccionada.
                    if ($opcion) { // Si encuentro la opción, guardo sus detalles.
                        $criteriosUsuario[] = [
                            'pregunta_enunciado' => $preguntaObj->enunciado, // Guardo el enunciado para mapeos.
                            'valor_opcion' => $opcion->valor_opcion,
                            'texto_opcion' => $opcion->texto_opcion,
                        ];
                    }
                }
            } else {
                $opcion = $preguntaObj->opciones->firstWhere('valor_opcion', $valor_respuesta_iter); // Busco la opción.
                if ($opcion) {
                    $criteriosUsuario[] = [
                        'pregunta_enunciado' => $preguntaObj->enunciado,
                        'valor_opcion' => $opcion->valor_opcion,
                        'texto_opcion' => $opcion->texto_opcion,
                    ];
                } else if ($preguntaObj->tipo_input === 'text' || $preguntaObj->tipo_input === 'textarea') { // Si es texto libre.
                    $criteriosUsuario[] = [
                        'pregunta_enunciado' => $preguntaObj->enunciado,
                        'valor_opcion' => strtolower(trim($valor_respuesta_iter)),
                        'texto_opcion' => $valor_respuesta_iter,
                    ];
                }
            }
        }


        // Llamo a mi función de estrategia con los datos preparados.
        $seleccion = $this->nuevaEstrategiaSeleccionProducto(
            $categoriaProductoElegida,
            $criteriosUsuario
        );

        // Obtengo el producto recomendado y la justificación del resultado de la estrategia.
        $productoRecomendado = $seleccion['producto'];
        $justificacionDetalle = $seleccion['justificacion'];
        $recomendacionCreada = null;
        // Si la estrategia encontró un producto
        if ($productoRecomendado) {
            // creo un nuevo registro de recomendación en la base de datos.
            $recomendacionCreada = Recomendacion::create([
                'envio_id' => $envio->id,
                'id_producto' => $productoRecomendado->idproducto, //
                'justificacion_titulo' => "Sugerencia para ti: " . htmlspecialchars($productoRecomendado->nombre),
                'justificacion_detalle' => $justificacionDetalle,
            ]);
        }

        // Si se creó la recomendación en la base de datos...
        if ($recomendacionCreada) {
            // redirijo al usuario a la página de "gracias", indicando que fue un éxito
            // y pasando el ID de la recomendación y del cuestionario.
            return redirect()->route('cuestionarios.gracias', [
                'nick' => $nick,
                'recomendacionId' => $recomendacionCreada->id
            ])
                ->with('success', '¡Cuestionario enviado! ' . htmlspecialchars($nick) . ', estamos preparando tu recomendación personalizada...')
                ->with('id_cuestionario_procesado', $id_cuestionario);
        } else {
            // Si no se pudo crear una recomendación (porque no se encontró un producto adecuado)...
            // preparo un mensaje informando al usuario y lo redirijo a la página de "gracias".
            // Uso 'info' en lugar de 'success' porque no es un éxito completo.
            $mensajeNoEncontrado = '¡Gracias por tus respuestas, ' . htmlspecialchars($nick) . '! De momento, no hemos encontrado un producto que se ajuste perfectamente a tus selecciones. Por favor, considera ajustar tus criterios o explora nuestra gama general de productos.';
            return redirect()->route('cuestionarios.gracias', [
                'nick' => $nick
            ])
                ->with('info', $mensajeNoEncontrado)
                ->with('id_cuestionario_procesado', $id_cuestionario);
        }
    }

    public function mostrarPaginaGracias($nick, $recomendacionId = null)
    {
        // Reviso si hay un mensaje 'info' o 'success' en la sesión para mostrarlo.
        $mensajeFlash = session('info') ?? session('success'); //
        $tipoMensaje = session('info') ? 'info' : (session('success') ? 'success' : null);

        // Muestro la vista 'cuestionarios.gracias' y le paso los datos necesarios.
        return view('cuestionarios.gracias', [
            'nick' => $nick,
            'recomendacionId' => $recomendacionId, //
            'id_cuestionario_actual' => session('id_cuestionario_procesado'), //
            'mensajeFlash' => $mensajeFlash,
            'tipoMensaje' => $tipoMensaje
        ]);
    }
}
