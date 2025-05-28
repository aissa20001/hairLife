<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function mostrarLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('Nombre', 'Clave');

        $usuario = Usuario::where('Nombre', $credentials['Nombre'])
            ->where('Clave', $credentials['Clave'])
            ->first();

        if ($usuario) {
            session([
                'usuario_codigo' => $usuario->Codigo,
                'usuario_nombre' => $usuario->Nombre, //Nombre usado para login
                'usuario_rol' => $usuario->Rol,
                'usuario_display_nick' => $usuario->nick // Guardamos  el nick de display (columna 'nick')
            ]);

            $urlDestino = '/';

            if ($usuario->Rol == 1) { // Admin
                $urlDestino = url('/admin');
            } elseif ($usuario->Rol == 0) { // Usuario normal
                // Redirige al panel usando el 'Nombre' del usuario
                // como el parámetro 'nick' para la ruta 'user.dashboard'.
                $dashboardUrl = route('user.dashboard', ['nick' => $usuario->Nombre]);



                if (!$usuario->nick) {
                    // Si no tiene nick, necesita completar el nick de display
                    session(['necesita_completar_nick_display' => true]);
                    // Guardamos la URL del dashboard a la que deberá ir DESPUÉS de crear el nick
                    session(['url_intended_after_nick' => $dashboardUrl]);
                    // Redirigimos a la página de creación de nick
                    return redirect()->route('crear.nick')
                        ->with('info', 'Por favor, elige un nick para tu perfil.');
                }
                // Si ya tiene nick, va directamente al dashboard
                $urlDestino = $dashboardUrl;
            }
            return redirect($urlDestino);
        }

        return back()->withErrors(['login' => 'Nombre o clave incorrectos']);
    }

    public function logout()
    {
        //Borra todas las variables de sesión almacenadas para ese usuario.
        session()->flush();
        return redirect('/login');
    }

    public function mostrarNick()
    {
        // Si el usuario esta en la sesión, y llega aquí
        //  tiene que introducir su nick de display.
        if (!session('usuario_codigo')) {
            // Si no hay usuario en sesión,lo mandamos a login.
            return redirect()->route('login')->with('error', 'Debes iniciar sesión primero.');
        }
        return view('crearNick');
    }

    public function guardarNick(Request $request)
    {
        $request->validate([
            'nick' => 'required|string|max:50',
        ]);
        //Busca un registro en la tabla usuarios cuyo ID o código coincida con el que está almacenado en la sesión (clave: usuario_codigo).
        $usuario = Usuario::find(session('usuario_codigo'));

        if ($usuario) {
            $usuario->nick = $request->input('nick'); // Actualiza la columna 'nick'
            $usuario->save();

            // Actualiza el nick de display en la sesión también
            session(['usuario_display_nick' => $usuario->nick]);
            session()->forget('necesita_completar_nick_display'); // Quita el flag si existía

            // Redirige al panel usando el 'Nombre' del usuario para la ruta
            return redirect()->route('user.dashboard', ['nick' => $usuario->Nombre])
                ->with('success', 'Nick actualizado correctamente.');
        }

        return back()->withErrors(['error' => 'Error al guardar el nick. Usuario no encontrado o sesión expirada.']);
    }
}
