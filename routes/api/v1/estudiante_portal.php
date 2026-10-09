<?php

use App\Http\Controllers\Api\EstudiantePortalController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('estudiante')->group(function () {
    Route::get('/dashboard', [EstudiantePortalController::class, 'dashboard']);
    Route::get('/mis-cursos', [EstudiantePortalController::class, 'misCursos']);
    Route::get('/mis-certificados', [EstudiantePortalController::class, 'misCertificados']);
    Route::get('/mis-pagos', [EstudiantePortalController::class, 'misPagos']);
    Route::get('/catalogo', [EstudiantePortalController::class, 'catalogo']);
    Route::post('/inscribirse-gratis', [EstudiantePortalController::class, 'inscribirGratis'])
        ->middleware('throttle:10,1');

    // Pago con comprobante: queda en revisión hasta que Caja lo apruebe
    Route::get('/bancos', [EstudiantePortalController::class, 'bancos']);
    Route::get('/pago-info/{idIns}', [EstudiantePortalController::class, 'infoPago'])->whereNumber('idIns');
    Route::post('/comprobantes', [EstudiantePortalController::class, 'enviarComprobante'])
        ->middleware('throttle:10,1');
    Route::post('/inscribirse', [EstudiantePortalController::class, 'inscribirConComprobante'])
        ->middleware('throttle:10,1');
    Route::post('/inscripciones/{idIns}/plan', [EstudiantePortalController::class, 'asignarPlan'])
        ->whereNumber('idIns')->middleware('throttle:10,1');
});
