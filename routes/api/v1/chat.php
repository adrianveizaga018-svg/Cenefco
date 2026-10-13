<?php

use App\Http\Controllers\Api\ChatController;
use Illuminate\Support\Facades\Route;

// Chat interno del personal (permiso chat.usar). Crear grupos y publicar avisos: chat.administrar (se valida en el servicio)
Route::middleware(['auth:sanctum', 'permiso:chat.usar'])->prefix('chat')->group(function () {
    Route::get('/conversaciones', [ChatController::class, 'conversaciones']);
    Route::get('/no-leidos', [ChatController::class, 'noLeidos']);
    Route::get('/directorio', [ChatController::class, 'directorio']);
    Route::get('/buscar', [ChatController::class, 'buscar']);
    Route::post('/directo', [ChatController::class, 'directo']);
    Route::post('/grupos', [ChatController::class, 'crearGrupo']);
    Route::patch('/grupos/{id}', [ChatController::class, 'actualizarGrupo'])->whereNumber('id');
    Route::get('/conversaciones/{id}/mensajes', [ChatController::class, 'mensajes'])->whereNumber('id');
    Route::post('/conversaciones/{id}/mensajes', [ChatController::class, 'enviar'])->whereNumber('id')->middleware('throttle:60,1');
    Route::post('/conversaciones/{id}/leido', [ChatController::class, 'leido'])->whereNumber('id');
    Route::delete('/mensajes/{idMensaje}', [ChatController::class, 'eliminar'])->whereNumber('idMensaje');
    Route::get('/mensajes/{idMensaje}/adjunto', [ChatController::class, 'adjunto'])->whereNumber('idMensaje');
});
