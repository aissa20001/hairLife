<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
// Asegúrate de tener estos 'use' si los necesitas dentro de los métodos
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
            ->where('Clave', $credentials['Clave']) // ¡RECUERDA HASHEAR CLAVES EN PRODUCCIÓN!
            ->first();

        if ($usuario) {
            session([
                'usuario_codigo' => $usuario->Codigo,
                'usuario_nombre' => $usuario->Nombre, // Este es el Nombre usado para login
                'usuario_rol' => $usuario->Rol,
                'usuario_display_nick' => $usuario->nick // Guardamos también el nick de display (columna 'nick')
            ]);

            $urlDestino = '/'; // Fallback genérico

            if ($usuario->Rol == 1) { // Admin
                $urlDestino = url('/admin'); // Asegúrate que la ruta /admin exista
            } elseif ($usuario->Rol == 0) { // Usuario normal
                // Redirigir SIEMPRE al dashboard usando el 'Nombre' del usuario
                // como el parámetro 'nick' para la ruta 'user.dashboard'.
                $urlDestino = route('user.dashboard', ['nick' => $usuario->Nombre]);

                // Opcional: si la columna 'nick' de la BD está vacía,
                // podrías añadir un flag a la sesión para recordarle al usuario que lo complete en su panel.
                if (!$usuario->nick) {
                    session(['necesita_completar_nick_display' => true]);
                }
            }
            return redirect($urlDestino);
        }

        return back()->withErrors(['login' => 'Nombre o clave incorrectos']);
    }

    public function logout()
    {
        session()->flush();
        return redirect('/login');
    }

    public function mostrarNick()
    {
        // Si el usuario ya está logueado (tiene 'usuario_codigo' en sesión)
        // y llega aquí, es porque quiere establecer/cambiar su nick de display.
        if (!session('usuario_codigo')) {
            // Si no hay usuario en sesión, no debería estar aquí, lo mandamos a login.
            return redirect()->route('login')->with('error', 'Debes iniciar sesión primero.');
        }
        return view('crearNick');
    }

    public function guardarNick(Request $request)
    {
        $request->validate([
            'nick' => 'required|string|max:50', // Puedes añadir unique:usuarios,nick si quieres que sea único
        ]);

        $usuario = Usuario::find(session('usuario_codigo'));

        if ($usuario) {
            $usuario->nick = $request->input('nick'); // Actualiza la columna 'nick'
            $usuario->save();

            // Actualiza el nick de display en la sesión también
            session(['usuario_display_nick' => $usuario->nick]);
            session()->forget('necesita_completar_nick_display'); // Quita el flag si existía

            // Redirige al dashboard usando el 'Nombre' del usuario para la ruta
            return redirect()->route('user.dashboard', ['nick' => $usuario->Nombre])
                ->with('success', 'Nick actualizado correctamente.');
        }

        return back()->withErrors(['error' => 'Error al guardar el nick. Usuario no encontrado o sesión expirada.']);
    }
}
