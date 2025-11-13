<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login para usuarios
     */
    public function showUserLogin()
    {
        return view('auth.user-login');
    }

    /**
     * Mostrar formulario de login para administradores
     */
    public function showAdminLogin()
    {
        return view('auth.admin-login');
    }

    /**
     * Mostrar formulario de registro
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Procesar login de usuario
     */
    public function userLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Verificar que el usuario existe y es cliente
        $usuario = Usuario::where('email', $credentials['email'])
                         ->where('tipo', 'cliente')
                         ->where('estado', 'activo')
                         ->first();

        if (!$usuario) {
            return back()->withErrors([
                'email' => 'Credenciales inválidas o acceso no autorizado.',
            ])->withInput($request->only('email'));
        }

        // Intentar autenticar
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $usuario = Usuario::where('email', $credentials['email'])->first();
            // Actualizar último login
            $usuario->update(['last_login_at' => now()]);
            // Notificación de bienvenida
            \App\Models\Notificacion::create([
                'usuario_id' => $usuario->id,
                'titulo' => '¡Bienvenido de nuevo!',
                'mensaje' => 'Has iniciado sesión exitosamente en Retrolector.',
                'tipo' => 'info',
                'leida' => false
            ]);
            
            // Redirigir al dashboard de usuario
            return redirect()->route('user.dashboard')->with('success', '¡Bienvenido de vuelta, ' . $usuario->nombre . '!');
        }

        return back()->withErrors([
            'password' => 'La contraseña es incorrecta.',
        ])->withInput($request->only('email'));
    }

    /**
     * Procesar login de administrador
     */
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Verificar que el usuario existe y es admin
        $usuario = Usuario::where('email', $credentials['email'])
                         ->where('tipo', 'admin')
                         ->where('estado', 'activo')
                         ->first();

        if (!$usuario) {
            return back()->withErrors([
                'email' => 'Credenciales inválidas o acceso no autorizado.',
            ])->withInput($request->only('email'));
        }

        // Intentar autenticar
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Actualizar último login
            $usuario->update(['last_login_at' => now()]);
            
            // Crear notificación de inicio de sesión
            \App\Models\Notificacion::create([
                'usuario_id' => $usuario->id,
                'titulo' => 'Inicio de sesión exitoso',
                'mensaje' => 'Has iniciado sesión correctamente en Retrolector.',
                'tipo' => 'success',
            ]);
            
            // Redirigir al dashboard de admin
            return redirect()->route('admin.dashboard')->with('success', '¡Bienvenido al panel de administración, ' . $usuario->nombre . '!');
        }

        return back()->withErrors([
            'password' => 'La contraseña es incorrecta.',
        ])->withInput($request->only('email'));
    }

    /**
     * Procesar registro de usuario
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'password' => 'required|string|min:8|confirmed',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo' => 'cliente',
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'idioma_preferencia' => 'es',
            'tema_preferencia' => 'claro',
            'estado' => 'activo',
        ]);

        Auth::login($usuario);

        return redirect()->route('user.dashboard')->with('success', '¡Bienvenido a Retrolector! Tu cuenta ha sido creada exitosamente.');
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')->with('success', 'Has cerrado sesión correctamente.');
    }

    /**
     * Cambiar idioma
     */
    public function switchLanguage($locale)
    {
        if (in_array($locale, ['es', 'en'])) {
            app()->setLocale($locale);
            
            if (Auth::check()) {
                Auth::user()->update(['idioma_preferencia' => $locale]);
            }
            
            Session::put('locale', $locale);
        }

        return redirect()->back();
    }

    /**
     * Cambiar tema
     */
    public function switchTheme($theme)
    {
        if (in_array($theme, ['claro', 'oscuro'])) {
            Session::put('theme', $theme);
            
            if (Auth::check()) {
                Auth::user()->update(['tema_preferencia' => $theme]);
            }
        }

        return redirect()->back();
    }
}
