<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SetTheme
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Establecer tema
        $theme = null;
        
        // 1. Verificar si hay un tema en la sesión
        if (Session::has('theme')) {
            $theme = Session::get('theme');
        }
        // 2. Si el usuario está autenticado, usar su preferencia
        elseif (auth()->check() && auth()->user()->tema_preferencia) {
            $theme = auth()->user()->tema_preferencia;
            Session::put('theme', $theme);
        }
        // 3. Usar el tema por defecto
        else {
            $theme = 'light';
            Session::put('theme', $theme);
        }
        
        // Validar que el tema esté soportado
        if (!in_array($theme, ['light', 'dark'])) {
            $theme = 'light';
            Session::put('theme', $theme);
        }
        
        // Compartir el tema con todas las vistas
        view()->share('theme', $theme);
        
        return $next($request);
    }
}
