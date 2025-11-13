<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Cambiar el idioma de la aplicación
     */
    public function switch($locale)
    {
        // Validar que el idioma esté soportado
        $supportedLocales = ['es', 'en'];
        
        if (!in_array($locale, $supportedLocales)) {
            return redirect()->back()->with('error', 'Idioma no soportado');
        }

        // Establecer el idioma de forma más directa
        Session::put('locale', $locale);
        App::setLocale($locale);
        config(['app.locale' => $locale]);
        
        // Log para debug
        \Log::info("LanguageController: Idioma cambiado a {$locale}");
        
        // Si el usuario está autenticado, actualizar su preferencia en la base de datos
        if (auth()->check()) {
            auth()->user()->update(['idioma_preferencia' => $locale]);
        }

        // Redirigir a la página principal para forzar la recarga completa
        $response = redirect()->route('home')->with('success', __('messages.language_changed'));
        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', '0');
        
        return $response;
    }
} 