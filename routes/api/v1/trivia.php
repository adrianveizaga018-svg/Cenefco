<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('trivia')->group(function () {
    Route::post('/partidas', [\App\Http\Controllers\Api\TriviaJuegoController::class, 'iniciar']);
    Route::post('/partidas/{partidaId}/responder', [\App\Http\Controllers\Api\TriviaJuegoController::class, 'responder']);

    Route::get('/saldo', [\App\Http\Controllers\Api\TriviaCanjeController::class, 'saldo']);
    Route::post('/canjes', [\App\Http\Controllers\Api\TriviaCanjeController::class, 'canjear']);
    Route::get('/canjes', [\App\Http\Controllers\Api\TriviaCanjeController::class, 'misCanjes']);

    Route::post('/duelos', [\App\Http\Controllers\Api\TriviaDueloController::class, 'crear']);
    Route::post('/duelos/unirse', [\App\Http\Controllers\Api\TriviaDueloController::class, 'unirse']);
    Route::get('/duelos/{partidaId}/estado', [\App\Http\Controllers\Api\TriviaDueloController::class, 'estado']);
    Route::post('/duelos/{partidaId}/responder', [\App\Http\Controllers\Api\TriviaDueloController::class, 'responder']);
});
