<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CobranzaController;

Route::middleware(['auth:sanctum'])->prefix('cobranzas')->group(function () {
    Route::get('/', [CobranzaController::class, 'dashboard'])
        ->middleware('permiso:pagos.ver');
});
