<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;

class LoginController extends Controller
{


    public function mostrarLogin()
    {
        return view('login'); // Asegúrate de tener login.blade.php
    }

    public function login(Request $request)
    {
        $credentials = $request->only('Nombre', 'Clave');

        $usuario = Usuario::where('Nombre', $credentials['Nombre'])
            ->where('Clave', $credentials['Clave'])
            ->first();

        if ($usuario) {
            // Iniciar sesión con datos del usuario
            session([
                'usuario_codigo' => $usuario->Codigo,
                'usuario_nombre' => $usuario->Nombre,
                'usuario_rol' => $usuario->Rol
            ]);
            // Determinar la URL de redirección según el rol y si tiene nick
            $urlDestino = '/dashboard'; // Valor por defecto

            if ($usuario->Rol == 1) {
                $urlDestino = url('/admin'); // Rol 1 (Admin) redirige a /admin
            } elseif ($usuario->Rol == 0) {
                if (!$usuario->nick) {
                    $urlDestino = url('/crear-nick'); // Rol 0 sin nick, redirige a crear nick
                } else {
                    $urlDestino = route('user.dashboard', ['nick' => $usuario->nick]); // Rol 0 con nick, redirige al dashboard con nick
                }
            }

            return redirect($urlDestino); // Redirigir a la URL correspondiente
        }

        return back()->withErrors(['login' => 'Nombre o clave incorrectos']);
    }
    protected function authenticated(Request $request, $user)
    {
        // Redirigir a la página para ingresar el nick.
        return redirect()->route('cuestionario.mostrar_formulario_nick');
    }

    public function logout()
    {
        session()->flush(); // Limpiar la sesión
        return redirect('/login'); // Redirigir al login
    }
    public function mostrarNick()
    {
        return view('crearNick');  // Vista para mostrar el formulario de creación de nick
    }
    public function guardarNick(Request $request)
    {
        $request->validate([
            'nick' => 'required|string|max:50',  // Validación básica del nick
        ]);
        $nick = '';
        $usuario = Usuario::find(session('usuario_codigo'));

        if ($usuario) {
            // Guardar el nick en la base de datos
            $usuario->nick = $request->input('nick');
            $usuario->save();
            $nick = $request->input('nick');

            // Redirigir al cliente al panel después de guardar el nick
            return redirect()->route('user.dashboard', ['nick' => $nick]);  // O la URL que corresponda
        }

        return back()->withErrors(['error' => 'Error al guardar el nick.']);
    }
}
