<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importar Auth
use Symfony\Component\HttpFoundation\Response;

class CheckAgentRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está autenticado y su rol es 'agent'
        if (Auth::check() && Auth::user()->role === 'agent') {
            // Déjalo pasar a la siguiente ruta
            return $next($request);
        }

        // Si no es un agente, redirígelo a la vista para aplicar
        return redirect()->route('agent.view')->with('error', 'Debes ser un agente para publicar propiedades.');
    }
}
