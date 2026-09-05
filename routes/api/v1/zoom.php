<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('zoom')->group(function () {
    
    Route::get('/cuentas',           [\App\Http\Controllers\Api\ZoomController::class, 'cuentas'])
        ->middleware('permiso:zoom.ver');
    Route::post('/cuentas',          [\App\Http\Controllers\Api\ZoomController::class, 'storeCuenta'])
        ->middleware('permiso:zoom.crear');
    Route::put('/cuentas/{id}',      [\App\Http\Controllers\Api\ZoomController::class, 'updateCuenta'])
        ->middleware('permiso:zoom.editar');
    Route::delete('/cuentas/{id}',   [\App\Http\Controllers\Api\ZoomController::class, 'destroyCuenta'])
        ->middleware('permiso:zoom.eliminar');
    Route::post('/cuentas/{id}/predeterminada', [\App\Http\Controllers\Api\ZoomController::class, 'setPredeterminada'])
        ->middleware('permiso:zoom.editar');
    Route::post('/cuentas/{id}/test',[\App\Http\Controllers\Api\ZoomController::class, 'testCuenta'])
        ->middleware('permiso:zoom.ver');

    
    Route::get('/meetings',   [\App\Http\Controllers\Api\ZoomController::class, 'meetings'])
        ->middleware('permiso:zoom.ver');
    Route::post('/meetings',  [\App\Http\Controllers\Api\ZoomController::class, 'crearReunion'])
        ->middleware('permiso:zoom.crear');
    Route::get('/recordings', [\App\Http\Controllers\Api\ZoomController::class, 'recordings'])
        ->middleware('permiso:zoom.ver');
});
