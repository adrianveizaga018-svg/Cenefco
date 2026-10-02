<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermiso
{
    public function handle(Request $request, Closure $next, string $permiso): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'No autenticado.'], 401);
        }

        if (! $user->tienePermiso($permiso)) {
            return response()->json([
                'error' => 'Acceso denegado.',
                'permiso' => $permiso,
            ], 403);
        }

        return $next($request);
    }
}
