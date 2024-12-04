<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckEmpresa
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado
        if (Auth::check()) {
            // Verificar si hay una empresa en la sesión
            if (!$request->session()->has('empresa')) {
                // Redirigir a una página de selección de empresa o error
                return redirect()->route('login')->withErrors(['empresa' => 'No tiene ninguna empresa asignada.']);
            }
        }

        return $next($request);
    }
}
