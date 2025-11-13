<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClienteMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión para acceder a esta sección.');
        }

        if (!auth()->user()->isCliente()) {
            return redirect()->route('home')->with('error', 'Acceso denegado. Solo clientes pueden acceder a esta sección.');
        }

        return $next($request);
    }
}
