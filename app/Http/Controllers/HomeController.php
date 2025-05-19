<?php

namespace App\Http\Controllers;

use App\Models\Cuestionario;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Muestra el panel principal para un nick específico.
     */
    public function mostrar($nick) // Coincide con la ruta 'user.dashboard'
    {
        // Busca el primer cuestionario activo para enlazarlo.
        $cuestionarioOficial = Cuestionario::where('estado', 'ACTIVO')->orderBy('id')->first();

        // La vista se llama 'user_dashboard.show' (asumiendo que renombraste home.blade.php)
        return view('user_dashboard.show', [
            'nick' => $nick,
            'cuestionarioOficial' => $cuestionarioOficial
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

    /**
     * (Opcional) Método para la ruta '/' si lo manejas aquí.
     */
    public function index()
    {
        return view('welcome'); // O redirigir a login/crear nick
    }
}
