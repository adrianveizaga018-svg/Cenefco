<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CobranzaController;
use App\Http\Controllers\Api\CobranzaDashboardController;

Route::middleware(['auth:sanctum'])->prefix('cobranzas')->group(function () {
    Route::get('/', [CobranzaController::class, 'dashboard'])
        ->middleware('permiso:pagos.ver');
    Route::get('/metrics', [CobranzaDashboardController::class, 'metrics'])->middleware('permiso:pagos.ver');
});
