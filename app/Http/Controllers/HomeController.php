<?php

namespace App\Http\Controllers;

use App\Models\Cuestionario;
use App\Models\Usuario; // ¡ASEGÚRATE DE IMPORTAR EL MODELO USUARIO!
use Illuminate\Http\Request; // No se usa en los métodos actuales, pero es buena práctica tenerlo si lo añades.

class HomeController extends Controller
{
    public function mostrar($nickDeURL) // Este $nickDeURL será el 'Nombre' del usuario
    {
        // Busca al usuario por su 'Nombre' que viene en la URL
        $usuario = Usuario::where('Nombre', $nickDeURL)->firstOrFail();
        // Busca el primer cuestionario activo, ordenado por su id
        $cuestionarioOficial = Cuestionario::where('estado', 'ACTIVO')->orderBy('id')->first();

        // La vista se llama 'user_dashboard.show'
        return view('user_dashboard.show', [
            'nick' => $usuario->Nombre, // El Nombre principal que identifica al usuario
            'displayNick' => $usuario->nick, // El nick de display (de la columna 'nick'), puede ser null
            'cuestionarioOficial' => $cuestionarioOficial,
            // Puedes añadir una variable para indicar si necesita completar el nick de display
            'necesitaCompletarNick' => session('necesita_completar_nick_display', false)
        ]);
    }

    /**
     * Muestra la página placeholder para "Mi Pelo".
     */
    public function showMiPelo($nick) // Coincide con la ruta 'user.mipelo' (corregida)
    {
        // Asegúrate que la vista 'user_dashboard.mi_pelo' existe
        return view('user_dashboard.mi_pelo', ['nick' => $nick]);
    }

    /**
     * Muestra la página placeholder para "Peinados y Cortes".
     */
    public function showPeinadosCortes($nick) // Coincide con la ruta 'user.peinadoscortes' (corregida)
    {
        // Asegúrate que la vista 'user_dashboard.peinados_cortes' existe
        return view('user_dashboard.peinados_cortes', ['nick' => $nick]);
    }
}
