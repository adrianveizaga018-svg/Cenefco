<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CajaController;

Route::middleware(['auth:sanctum'])->prefix('caja')->group(function () {
    Route::get('/buscar-estudiante', [CajaController::class, 'buscarEstudiante'])
        ->middleware('permiso:inscripciones.crear');
    Route::get('/programas', [CajaController::class, 'buscarProgramas'])
        ->middleware('permiso:inscripciones.crear');
    Route::post('/inscribir', [CajaController::class, 'inscribir'])
        ->middleware('permiso:inscripciones.crear');
    Route::get('/bancos', [CajaController::class, 'bancos'])
        ->middleware('permiso:inscripciones.crear');
});