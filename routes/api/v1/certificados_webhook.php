<?php

use Illuminate\Support\Facades\Route;

/**
 * Webhook de integración con el sistema externo de certificados (Django/Celery).
 * No requiere autenticación Sanctum, pero valida un secreto compartido en el header.
 * Registrado en routes/api/v1.php via require.
 */
Route::post(
    '/certificados/webhook-lote',
    [\App\Http\Controllers\Api\CertificadoWebhookController::class, 'recibirLote']
)->middleware('throttle:30,1');
