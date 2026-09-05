<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\VerifyEmailController;
use App\Http\Controllers\Api\PagoOnlineSessionController;
use App\Http\Controllers\Api\PagoPublicoController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => response()->json(['status' => 'ok', 'message' => 'API funcionando']));

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login');
    Route::post('/register-publico', [AuthController::class, 'registerPublico'])
        ->middleware('throttle:login');
    Route::post('/google', [AuthController::class, 'loginGoogle'])
        ->middleware('throttle:login');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
        ->middleware('throttle:forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:5,15');
});

Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/complete-profile', [AuthController::class, 'completeProfile']);
    Route::put('/perfil', [AuthController::class, 'updatePerfil']);
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('permiso:usuarios.crear');
});

Route::middleware('auth:sanctum')->prefix('email')->group(function () {
    Route::get('/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');
    Route::post('/resend', [VerifyEmailController::class, 'resend'])
        ->middleware('throttle:6,1');
});

Route::prefix('v1')->group(base_path('routes/api/v1.php'));

Route::post('/pagosya/webhook', [PagoOnlineSessionController::class, 'webhook'])
    ->withoutMiddleware(['auth:sanctum']);

Route::middleware('throttle:60,1')->prefix('public')->group(function () {
    Route::post('/pago/iniciar',              [PagoPublicoController::class, 'iniciar']);
    Route::get('/pago/estado/{session_id}',   [PagoPublicoController::class, 'estado']);
});

Route::prefix('whatsapp')->group(function () {
    Route::get('/webhook',  [\App\Http\Controllers\Api\WhatsApp\WhatsAppController::class, 'verify']);
    Route::post('/webhook', [\App\Http\Controllers\Api\WhatsApp\WhatsAppController::class, 'receive']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/test',     [\App\Http\Controllers\Api\WhatsApp\WhatsAppController::class, 'test'])
            ->middleware('permiso:whatsapp.ver');
        Route::get('/bot-test', [\App\Http\Controllers\Api\WhatsApp\WhatsAppController::class, 'botTest'])
            ->middleware('permiso:whatsapp.ver');
    });
});
