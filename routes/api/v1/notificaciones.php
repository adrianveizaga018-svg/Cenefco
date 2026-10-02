<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('notificaciones')->group(function () {
    
    Route::get('/no-leidas',          [\App\Http\Controllers\Api\NotificacionController::class, 'noLeidas']);
    Route::put('/leer-todas',         [\App\Http\Controllers\Api\NotificacionController::class, 'marcarTodasLeidas']);
    Route::get('/stream',             [\App\Http\Controllers\Api\NotificacionController::class, 'stream']);
    Route::get('/preferencias',       [\App\Http\Controllers\Api\NotificacionController::class, 'preferencias']);
    Route::put('/preferencias',       [\App\Http\Controllers\Api\NotificacionController::class, 'guardarPreferencias']);
    Route::get('/enviados',           [\App\Http\Controllers\Api\NotificacionController::class, 'enviados']);
    Route::post('/comunicado',        [\App\Http\Controllers\Api\NotificacionController::class, 'comunicado'])
        ->middleware('permiso:usuarios.ver');

    
    Route::get('/',                   [\App\Http\Controllers\Api\NotificacionController::class, 'index']);
    Route::get('/{id}',               [\App\Http\Controllers\Api\NotificacionController::class, 'show']);
    Route::put('/{id}/leer',          [\App\Http\Controllers\Api\NotificacionController::class, 'marcarLeida']);
    Route::delete('/{id}',            [\App\Http\Controllers\Api\NotificacionController::class, 'destroy']);
});
