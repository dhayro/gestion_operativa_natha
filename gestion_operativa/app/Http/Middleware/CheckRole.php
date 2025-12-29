<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $usuario = auth()->user();

        if (!$usuario) {
            abort(401, 'No autenticado');
        }

        // Verificar si el usuario tiene alguno de los roles requeridos
        if ($usuario->hasAnyRole($roles)) {
            return $next($request);
        }

        abort(403, 'No tienes el rol necesario para acceder a este recurso');
    }
}
