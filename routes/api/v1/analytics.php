<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/analytics/stats', [\App\Http\Controllers\Api\VisitaController::class, 'stats'])
        ->middleware('permiso:reportes.ver');
});
