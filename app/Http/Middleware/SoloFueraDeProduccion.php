<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Herramientas de desarrollo (documentación de la API): en producción no existen.
 * Publicarlas mostraría a cualquiera el mapa completo de rutas del sistema.
 */
class SoloFueraDeProduccion
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_if(app()->environment('production') && ! config('l5-swagger.publico_en_produccion'), 404);

        return $next($request);
    }
}
