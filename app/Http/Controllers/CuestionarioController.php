<?php

namespace App\Http\Controllers;

use App\Models\Cuestionario;
use App\Models\CuestionarioEnvio;
use App\Models\Respuesta;
use App\Models\Usuario; // Descomenta si vas a usar el modelo Usuario
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; // Para la autenticación, si la usas
use Illuminate\Support\Facades\Validator; // Para validar los datos del formulario

class CuestionarioController extends Controller
{
    /**
     * Muestra una lista de cuestionarios activos. (Opcional)
     */
    public function listar()
    {
        $cuestionarios = Cuestionario::where('estado', 'ACTIVO')->orderBy('titulo')->get();
        return view('cuestionarios.listar', ['cuestionarios' => $cuestionarios]);
    }


    /**
     * Muestra un cuestionario específico con sus preguntas.
     * El parámetro $id viene de la URL (ej: /cuestionarios/1)
     */
    public function mostrarParaNick($nick, $id_cuestionario)
    {
        // Busca el cuestionario por su ID. Si no lo encuentra o no está ACTIVO, falla (muestra un error 404).
        $cuestionario = Cuestionario::where('id', $id_cuestionario)->where('estado', 'ACTIVO')->firstOrFail();

        // Carga las preguntas asociadas a este cuestionario, ordenadas por el campo 'numero' de la tabla pivote.
        // La relación 'preguntas' en el modelo Cuestionario ya se encarga del orden.
        $preguntas = $cuestionario->preguntas()->with('opciones')->get(); // Esto ya debería traerlas ordenadas por 'pivot_numero'

        // Pasa el cuestionario y sus preguntas a la vista 'cuestionarios.ver'
        return view('cuestionarios.ver_para_nick', [
            'nick' => $nick,
            'cuestionario' => $cuestionario,
            'preguntas' => $preguntas
        ]);
    }

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
            // Para radio y text 'required' es suficiente en la regla.
            // Para checkbox, si quieres que al menos uno sea seleccionado, la regla es 'required|array'.
            if ($pregunta->tipo_input === 'checkbox') {
                $rules['respuestas.' . $pregunta->id] = 'nullable|array'; // Permite no enviar nada si no es obligatorio, o espera un array
                // Si quisieras que al menos un checkbox sea obligatorio:
                // $rules['respuestas.' . $pregunta->id] = 'required|array|min:1';
                // $messages['respuestas.' . $pregunta->id . '.required'] = 'Debes seleccionar al menos una opción para: ' . htmlspecialchars($pregunta->enunciado);
                // $messages['respuestas.' . $pregunta->id . '.min'] = 'Debes seleccionar al menos una opción para: ' . htmlspecialchars($pregunta->enunciado);
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
        // Si usuario_codigo es NOT NULL en tu BD y $usuarioCodigo es null aquí, tendrás un error al crear el envío.
        // Por eso lo hice NULLABLE y ON DELETE SET NULL en el último script SQL.

        $envio = CuestionarioEnvio::create([
            'usuario_codigo' => $usuarioCodigo,
            'cuestionario_id' => $id_cuestionario,
            'nick_utilizado' => $nick,
        ]);

        foreach ($request->input('respuestas', []) as $id_pregunta => $valor_respuesta_o_array) {
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

        return redirect()->route('cuestionarios.graciasParaNick', ['nick' => $nick])
            ->with('success', '¡Cuestionario enviado con éxito, ' . htmlspecialchars($nick) . '!');
    }
}
