<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permiso' => \App\Http\Middleware\CheckPermiso::class,
            'solo.activos' => \App\Http\Middleware\SoloActivosPortal::class,
            'rate.portal' => \App\Http\Middleware\RateLimitPortal::class,
            'encrypt.portal' => \App\Http\Middleware\EncryptApiResponse::class,
            'portal.key' => \App\Http\Middleware\ValidarPortalKey::class,
        ]);

        $middleware->redirectGuestsTo(fn ($request) => $request->is('api/*') ? null : '/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\App\Shared\Kernel\Exceptions\DomainException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        });

        $exceptions->render(function (\RuntimeException $e, $request) {
            if (! ($request->expectsJson() || $request->is('api/*'))) {
                return null;
            }
            if ($e->getCode() === 404) {
                return response()->json(['error' => $e->getMessage()], 404);
            }

            return null;
        });

        $exceptions->render(function (\App\Domain\Ventas\Exceptions\VentaNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 404);
            }
        });

        $exceptions->render(function (\App\Domain\Productos\Exceptions\ProductoNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 404);
            }
        });

        $exceptions->render(function (\App\Domain\Productos\Exceptions\StockInsuficienteException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        });


        $exceptions->render(function (\App\Domain\Inscripciones\Exceptions\InscripcionDuplicadaException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        });

        $exceptions->render(function (\App\Domain\InscripcionesDiplomado\Exceptions\InscripcionDiplomadoDuplicadaException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        });

        $exceptions->render(function (\App\Domain\Planillas\Exceptions\PlanillaDuplicadaException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        });

        $exceptions->render(function (\App\Domain\Planillas\Exceptions\PlanillaNoEliminableException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        });

        $exceptions->render(function (\App\Domain\Gastos\Exceptions\GastoRecurrenteYaConfirmadoException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        });

        $exceptions->render(function (\App\Domain\AjustesSueldo\Exceptions\AjusteSueldoYaAplicadoException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        });

        $exceptions->render(function (\App\Domain\AjustesSueldo\Exceptions\AjustePeriodoYaPagadoException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        });

        $exceptions->render(function (\App\Domain\Pagos\Exceptions\PagoDuplicadoException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        });

        $exceptions->render(function (\App\Domain\Pagos\Exceptions\PagoBoletaDuplicadaException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        });

        $exceptions->render(function (\App\Domain\Pagos\Exceptions\DevolucionEstadoInvalidoException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        });

        $exceptions->render(function (\App\Domain\Pagos\Exceptions\DevolucionMontoExcedeDisponibleException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        });

        $exceptions->render(function (\App\Domain\Cursos\Exceptions\CursoConInscritosException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        });

        $exceptions->render(function (\App\Domain\ProgramasAcademicos\Exceptions\ProgramaAcademicoConInscritosException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        });

        $exceptions->render(function (\App\Domain\Imparticiones\Exceptions\ImparteConInscritosException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        });

        $exceptions->render(function (\App\Domain\CampanasPublicidad\Exceptions\CampanaPublicidadConGastosException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        });

        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Recurso no encontrado.'], 404);
            }
        });

        $exceptions->render(function (\Illuminate\Database\QueryException $e, $request) {
            if (! ($request->expectsJson() || $request->is('api/*'))) {
                return null;
            }

            // Integrity constraint violation (FK, unique, etc.)
            // '23000' = MySQL genérico; '23503'/'23505' = PostgreSQL FK/unique
            if (in_array($e->getCode(), ['23000', 23000, '23503', '23505'], true)) {
                $msg = match (true) {
                    str_contains($e->getMessage(), 'Cannot delete or update a parent row') => 'No se puede eliminar este registro porque tiene datos relacionados.',
                    str_contains($e->getMessage(), 'foreign key') || str_contains($e->getMessage(), 'llave foránea') => 'No se puede eliminar este registro porque tiene datos relacionados.',
                    str_contains($e->getMessage(), 'Duplicate entry') => 'Ya existe un registro con ese valor.',
                    str_contains($e->getMessage(), 'unique constraint') || str_contains($e->getMessage(), 'llave duplicada') => 'Ya existe un registro con ese valor.',
                    default => 'Violación de restricción de base de datos.',
                };

                return response()->json(['error' => $msg], 409);
            }

            return null;
        });

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'error' => 'Error de validación.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'No autenticado.'], 401);
            }
        });
    })->create();
