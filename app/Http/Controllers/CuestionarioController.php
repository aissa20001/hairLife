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
{
    // ... (el método listar permanece igual) ...
    // ... (el método mostrarParaNick permanece igual) ...

    /**
     * Procesa el envío de un formulario de cuestionario.
     * $id_cuestionario viene de la URL.
     * $request contiene todos los datos enviados por el formulario.
     */
    public function procesarEnvioParaNick(Request $request, $nick, $id_cuestionario)
    {
        $cuestionario = Cuestionario::findOrFail($id_cuestionario); // Asegurar que existe
        $preguntasDelCuestionario = $cuestionario->preguntas()->with('opciones')->get();

        // --- Validación ---
        $rules = [];
        $messages = [];
        foreach ($preguntasDelCuestionario as $pregunta) {
            if ($pregunta->tipo_input === 'checkbox') {
                $rules['respuestas.' . $pregunta->id] = 'nullable|array'; // Permite no enviar nada si no es obligatorio, o espera un array
            } else { // text, radio, textarea
                $rules['respuestas.' . $pregunta->id] = 'required';
                $messages['respuestas.' . $pregunta->id . '.required'] = 'La pregunta "' . htmlspecialchars(Str::limit($pregunta->enunciado, 50)) . '" es obligatoria.';
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->route('cuestionarios.mostrarParaNick', ['nick' => $nick, 'id_cuestionario' => $id_cuestionario])
                ->withErrors($validator)
                ->withInput();
        }
        // --- Fin Validación ---

        $usuario = Usuario::where('Nombre', $nick)->first();
        $usuarioCodigo = $usuario ? $usuario->Codigo : null;

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

        // --- INICIO: Lógica de Recomendación MEJORADA ---
        $productoRecomendado = null;
        $recomendacionCreada = null;
        $justificacionDetalle = "Hemos seleccionado este producto basado en tus respuestas generales.";

        // 1. Obtener respuesta para "¿Qué producto quieres que te recomendemos?"
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

            if ($keywordParaDescripcion) { // Solo si no fue manejado por el caso 'muy_rizado_afro'
                $queryProducto->where('descripcion', 'LIKE', '%' . $keywordParaDescripcion . '%');
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
        // --- FIN: Lógica de Recomendación MEJORADA ---

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
