<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
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
        // Establecer idioma de forma más directa
        $locale = Session::get('locale', 'es');
        
        // Validar y establecer el idioma
        if (in_array($locale, ['es', 'en'])) {
            App::setLocale($locale);
            config(['app.locale' => $locale]);
            
            // Debug: Log del idioma establecido
            \Log::info("Middleware SetLocale: Idioma establecido a {$locale}");
        } else {
            // Si no es válido, usar español por defecto
            App::setLocale('es');
            config(['app.locale' => 'es']);
            Session::put('locale', 'es');
            \Log::info("Middleware SetLocale: Idioma inválido, usando español por defecto");
        }
        
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
            $theme = 'claro';
            Session::put('theme', $theme);
        }
        
        // Validar que el tema esté soportado
        if (!in_array($theme, ['claro', 'oscuro'])) {
            $theme = 'claro';
            Session::put('theme', $theme);
        }
        
        return $next($request);
    }
}
