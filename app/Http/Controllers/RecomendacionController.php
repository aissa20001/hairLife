<?php

namespace App\Http\Controllers;

use App\Models\Recomendacion; // Asegúrate que tu modelo Recomendacion esté en App\Models
use Illuminate\Http\Request;

class RecomendacionController extends Controller
{
    /**
     * Muestra la página del producto recomendado.
     *
     * @param  int  $id  El ID del registro en la tabla 'recomendaciones'
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function verRecomendacion($id)
    {
        // Busca la recomendación por su ID y carga la relación 'producto'
        // La relación 'producto' debe estar definida en tu modelo App\Models\Recomendacion
        $recomendacion = Recomendacion::with('producto')->find($id); // Usamos find() para manejar el caso de no encontrado más abajo

        // Verifica si se encontró la recomendación y si tiene un producto asociado
        if (!$recomendacion || !$recomendacion->producto) {
            $userNick = session('usuario_nombre'); // O 'user_nick' si usas ese nombre de variable de sesión

            if ($userNick) {
                return redirect()->route('user.dashboard', ['nick' => $userNick])
                    ->with('error', 'La recomendación solicitada no está disponible o el producto no fue encontrado.');
            } else {
                // Fallback si no hay nick en sesión (quizás el usuario no está logueado o la sesión expiró)
                return redirect()->route('crear.nick') // O a la página de login
                    ->with('error', 'Error al cargar la recomendación. Por favor, inicia sesión o crea un nick.');
            }
        }

        // Pasa la recomendación y el producto a la vista
        return view('recomendaciones.ver_producto', [
            'recomendacion' => $recomendacion,
            'producto' => $recomendacion->producto
        ]);
    }
}
