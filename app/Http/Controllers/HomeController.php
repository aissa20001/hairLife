<?php

namespace App\Http\Controllers;

use App\Models\Cuestionario;
use App\Models\Usuario;
//use Illuminate\Http\Request; 

class HomeController extends Controller
{
    public function mostrar($nickDeURL) // Este parametro será el Nombre del usuario
    {
        // Busca al usuario por su 'Nombre' que viene en la URL
        $usuario = Usuario::where('Nombre', $nickDeURL)->firstOrFail();
        // Busca el primer cuestionario activo, ordenado por su id
        $cuestionarioOficial = Cuestionario::where('estado', 'ACTIVO')->orderBy('id')->first();

        // La vista se llama 'user_dashboard.show'
        return view('user_dashboard.show', [
            'nick' => $usuario->Nombre,
            'displayNick' => $usuario->nick,
            'cuestionarioOficial' => $cuestionarioOficial,
            'necesitaCompletarNick' => session('necesita_completar_nick_display', false)
        ]);
    }

    //Muestra la pagina de MiPelo (EN DESARROLLO)
    public function showMiPelo($nick)
    {
        //muestra la vista y le pasa el nick para que sepa a qué usuario pertenece
        return view('user_dashboard.mi_pelo', ['nick' => $nick]);
    }

    //Muestra la pagina de Peinados y Cortes (EN DESARROLLO)
    public function showPeinadosCortes($nick) // Coincide con la ruta 'user.peinadoscortes' (corregida)
    {

        return view('user_dashboard.peinados_cortes', ['nick' => $nick]);
    }
}
