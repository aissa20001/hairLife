<?php

namespace App\Http\Controllers;

use App\Models\Recomendacion;
//use Illuminate\Http\Request; // No la estoy usando aquí, así que la comento.
use App\Models\Pregunta; // Necesito Pregunta para pillar la de filtro.

class RecomendacionController extends Controller
{


    //Este método es para cuando el usuario hace clic para ver una recomendación específica.
    //Le llega el ID de la recomendación por la URL.

    public function verRecomendacion($id)
    {
        // Primero, intento cargar la recomendación con su producto asociado.
        // El `with('producto')` es para que me traiga el producto de una vez y no hacer otra query.
        // Uso `find()` en lugar de `findOrFail()` para poder manejar yo el caso de que no exista.
        $recomendacion = Recomendacion::with('producto')->find($id);

        // Ahora compruebo si encontré la recomendación Y si tiene un producto.
        // Si no hay recomendación o le falta el producto, algo fue mal.
        if (!$recomendacion || !$recomendacion->producto) {
            // Si falla, intento redirigir al dashboard del usuario si tengo su nick en sesión.
            $userNick = session('usuario_nombre'); // A ver si tengo el nick guardado.

            if ($userNick) {
                // Si tengo nick, lo mando a su dashboard con un mensaje de error.
                return redirect()->route('user.dashboard', ['nick' => $userNick])
                    ->with('error', 'La recomendación solicitada no está disponible o el producto no fue encontrado.');
            } else {
                // Si no hay nick en sesión (quizás no está logueado o la sesión caducó),
                // lo mando a la página de crear nick (o podría ser al login).
                return redirect()->route('crear.nick')
                    ->with('error', 'Error al cargar la recomendación. Por favor, inicia sesión o crea un nick.');
            }
        }

        // Si todo fue bien hasta aquí, tengo la recomendación y su producto.
        // Ahora quiero sacar el ID del cuestionario original y el ID del envío
        // para poder poner un enlace de "volver a rellenar" o "ver mis respuestas".
        $id_cuestionario_actual = null;
        $envio_id_para_link = null; // Este es el ID del CuestionarioEnvio

        // Me aseguro de que la recomendación tenga un 'envio' asociado.
        if ($recomendacion->envio) {
            $id_cuestionario_actual = $recomendacion->envio->cuestionario_id; // El ID del Cuestionario en sí.
            $envio_id_para_link = $recomendacion->envio->id; // El ID del registro CuestionarioEnvio.
        }

        // También necesito el ID de la pregunta filtro "¿Qué producto quieres que te recomendemos?"
        // por si quiero hacer algo especial con ella en la vista, como marcarla o no permitir cambiarla
        // si el usuario vuelve a rellenar el cuestionario desde aquí.
        $preguntaFiltroObj = Pregunta::where('enunciado', '¿Qué producto quieres que te recomendemos?')->first();
        $id_pregunta_filtro = $preguntaFiltroObj ? $preguntaFiltroObj->id : null;

        // Mando toda la info a la vista `ver_producto`.
        return view('recomendaciones.ver_producto', [
            'recomendacion' => $recomendacion, // La recomendación completa.
            'producto' => $recomendacion->producto, // El producto recomendado.
            'id_cuestionario_actual' => $id_cuestionario_actual, // Para saber de qué cuestionario vino.
            'id_pregunta_filtro' => $id_pregunta_filtro,   // El ID de la pregunta de categoría de producto.
            'envio_id_para_link_cuestionario' => $envio_id_para_link, // El ID del envío para el enlace de "volver a rellenar".
        ]);
    }
}
