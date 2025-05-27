<?php

namespace App\Http\Controllers;

use App\Models\Recomendacion; // Asegúrate que tu modelo Recomendacion esté en App\Models
use Illuminate\Http\Request;
use App\Models\Pregunta;

class RecomendacionController extends Controller
{
    /**
     * Muestra la página del producto recomendado.
     *
     * @param  int  $id  El ID del registro en la tabla 'recomendaciones'
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */

    //Este método es para mostrar una página de recomendación de producto

    public function verRecomendacion($id)
    {
        // Busca la recomendación por su ID y carga la relación 'producto'
        // La relación 'producto' debe estar definida en el modelo de Recomendacion
        $recomendacion = Recomendacion::with('producto')->find($id); // Usamos find() para manejar el caso de no encontrado más abajo

        // Verifica si se encontró la recomendación y si tiene un producto asociado
        if (!$recomendacion || !$recomendacion->producto) {
            // Redirigir según el estado de sesión
            $userNick = session('usuario_nombre');

            //Intenta obtener el nick del usuario desde la sesión.
            if ($userNick) {
                return redirect()->route('user.dashboard', ['nick' => $userNick])
                    ->with('error', 'La recomendación solicitada no está disponible o el producto no fue encontrado.');
            } else {
                // Fallback si no hay nick en sesión (quizás el usuario no está logueado o la sesión expiró)
                return redirect()->route('crear.nick') // O a la página de login
                    ->with('error', 'Error al cargar la recomendación. Por favor, inicia sesión o crea un nick.');
            }
        }
        // Obtener el ID del Cuestionario desde el envio de la recomendación
        $id_cuestionario_actual = null;
        if ($recomendacion->envio) {
            $id_cuestionario_actual = $recomendacion->envio->cuestionario_id; //
        }

        // Obtener el ID de la pregunta específica "¿Qué producto quieres que te recomendemos?"
        $preguntaFiltroObj = Pregunta::where('enunciado', '¿Qué producto quieres que te recomendemos?')->first(); //
        $id_pregunta_filtro = $preguntaFiltroObj ? $preguntaFiltroObj->id : null;
        // Pasa la recomendación y el producto a la vista
        return view('recomendaciones.ver_producto', [
            'recomendacion' => $recomendacion,
            'producto' => $recomendacion->producto,
            'id_cuestionario_actual' => $id_cuestionario_actual, // Variable para el ID del cuestionario
            'id_pregunta_filtro' => $id_pregunta_filtro       // Variable para el ID de la pregunta filtro
        ]);
    }
}
