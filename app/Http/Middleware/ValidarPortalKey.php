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
            return $next($request);
        }

        $keyRecibida = $request->header('X-Portal-Key');

        if (! hash_equals($keyEsperada, (string) $keyRecibida)) {
            return response()->json(['error' => 'No autorizado.'], 403);
        }

        return $next($request);
    }
}
