<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permisos): Response
    {
        $usuario = auth()->user();

        if (!$usuario) {
            abort(401, 'No autenticado');
        }

        // Verificar si el usuario tiene alguno de los permisos requeridos
        foreach ($permisos as $permiso) {
            if ($usuario->hasPermission($permiso)) {
                return $next($request);
            }
        }

        abort(403, 'No tienes permiso para acceder a este recurso');
    }
}
