<?php

use App\Http\Controllers\Api\SeguimientoCampanaController;
use Illuminate\Support\Facades\Route;

// Seguimiento de campañas por marketing: registro diario, resumen e informes en PDF
Route::middleware(['auth:sanctum'])->prefix('campanas-publicidad')->group(function () {
    Route::get('/{id}/seguimiento', [SeguimientoCampanaController::class, 'show'])->whereNumber('id')->middleware('permiso:campanas.ver');
    Route::post('/{id}/registro-diario', [SeguimientoCampanaController::class, 'guardar'])->whereNumber('id')->middleware('permiso:campanas.editar');
    Route::delete('/registro-diario/{idRegistro}', [SeguimientoCampanaController::class, 'eliminar'])->whereNumber('idRegistro')->middleware('permiso:campanas.editar');
    Route::post('/{id}/informes', [SeguimientoCampanaController::class, 'generarInforme'])->whereNumber('id')->middleware(['permiso:campanas.ver', 'throttle:20,1']);
});
