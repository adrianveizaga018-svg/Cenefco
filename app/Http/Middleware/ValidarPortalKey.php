<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidarPortalKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $keyEsperada = config('services.portal.api_key');

        if (empty($keyEsperada)) {
            // Sin clave configurada: abierto solo en desarrollo. En producción se cierra en vez de quedar público.
            if (app()->environment('production')) {
                return response()->json(['error' => 'Portal no configurado (falta PORTAL_API_KEY).'], 503);
            }

            return $next($request);
        }

        $keyRecibida = $request->header('X-Portal-Key');

        if (! hash_equals($keyEsperada, (string) $keyRecibida)) {
            return response()->json(['error' => 'No autorizado.'], 403);
        }

        return $next($request);
    }
}
