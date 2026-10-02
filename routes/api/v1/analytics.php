<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/analytics/stats', [\App\Http\Controllers\Api\VisitaController::class, 'stats'])
        ->middleware('permiso:reportes.ver');

    Route::get('/dashboard-gerencia', [\App\Http\Controllers\Api\DashboardGerencialController::class, 'index'])
        ->middleware('permiso:reportes.ver');
});
