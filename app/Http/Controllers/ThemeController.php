<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ThemeController extends Controller
{
    /**
     * Cambiar el tema de la aplicación
     */
    public function switch($theme)
    {
        // Validar que el tema esté soportado
        $supportedThemes = ['light', 'dark'];
        
        if (!in_array($theme, $supportedThemes)) {
            return redirect()->back()->with('error', 'Tema no soportado');
        }

        // Establecer el tema en la sesión
        Session::put('theme', $theme);
        
        // Si el usuario está autenticado, actualizar su preferencia en la base de datos
        if (auth()->check()) {
            auth()->user()->update(['tema_preferencia' => $theme]);
        }

        return redirect()->back()->with('success', __('messages.theme_changed'));
    }

    /**
     * Cambiar el tema de la aplicación (alias para compatibilidad con la ruta theme.change)
     */
    public function change($theme)
    {
        return $this->switch($theme);
    }
} 