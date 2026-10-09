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
    Route::post('/estudiantes/{idUs}/enviar-credenciales', [\App\Http\Controllers\Api\CredencialesEstudianteController::class, 'enviar'])
        ->whereNumber('idUs')
        ->middleware(['permiso:inscripciones.crear', 'throttle:10,1']);
    Route::get('/cuotas-pendientes/{ci}', [\App\Http\Controllers\Api\CobranzaController::class, 'cuotasPendientes'])
        ->middleware('permiso:inscripciones.crear');
    Route::post('/registrar-cuota', [\App\Http\Controllers\Api\CobranzaController::class, 'registrarPagoCuota'])
        ->middleware('permiso:inscripciones.crear');

    // Comprobantes que suben los estudiantes desde su panel
    Route::get('/comprobantes', [\App\Http\Controllers\Api\ComprobantesCajaController::class, 'index'])
        ->middleware('permiso:inscripciones.crear');
    Route::get('/comprobantes/pendientes-count', [\App\Http\Controllers\Api\ComprobantesCajaController::class, 'pendientes'])
        ->middleware('permiso:inscripciones.crear');
    Route::patch('/comprobantes/{idPago}/aprobar', [\App\Http\Controllers\Api\ComprobantesCajaController::class, 'aprobar'])
        ->whereNumber('idPago')->middleware('permiso:inscripciones.crear');
    Route::patch('/comprobantes/{idPago}/rechazar', [\App\Http\Controllers\Api\ComprobantesCajaController::class, 'rechazar'])
        ->whereNumber('idPago')->middleware('permiso:inscripciones.crear');
});