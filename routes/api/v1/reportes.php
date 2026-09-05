<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('reportes')->group(function () {
    Route::get('/ventas-por-periodo', [\App\Http\Controllers\Api\Reportes\ReporteController::class, 'ventasPorPeriodo'])
        ->middleware('permiso:reportes.ver');
    Route::get('/cuotas-curso', [\App\Http\Controllers\Api\Reportes\ReporteController::class, 'cuotasCurso'])
        ->middleware('permiso:reportes.ver');
});
