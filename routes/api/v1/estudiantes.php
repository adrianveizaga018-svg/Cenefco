<?php

use App\Http\Controllers\Api\FichaEstudianteController;
use Illuminate\Support\Facades\Route;

// Ficha del estudiante (Caja / personal académico) y emisión de certificado por inscripción
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/estudiantes/buscar', [FichaEstudianteController::class, 'buscar'])
        ->middleware('permiso:inscripciones.ver');
    Route::get('/estudiantes/{idUs}/ficha', [FichaEstudianteController::class, 'ficha'])
        ->whereNumber('idUs')
        ->middleware('permiso:inscripciones.ver');

    Route::get('/inscripciones/{idIns}/certificado-opciones', [FichaEstudianteController::class, 'opcionesCertificado'])
        ->whereNumber('idIns')
        ->middleware('permiso:inscripciones.ver');
    Route::post('/inscripciones/{idIns}/emitir-certificado', [FichaEstudianteController::class, 'emitirCertificado'])
        ->whereNumber('idIns')
        ->middleware('permiso:certificados.crear');
});

// Panel de devoluciones (saldo a favor por cambio de programa, bajas, etc.)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/devoluciones', [\App\Http\Controllers\Api\DevolucionesPanelController::class, 'index'])
        ->middleware('permiso:inscripciones.ver');
});
