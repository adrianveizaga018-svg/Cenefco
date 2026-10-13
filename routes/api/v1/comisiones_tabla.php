<?php

use App\Http\Controllers\Api\ComisionTablaController;
use Illuminate\Support\Facades\Route;

// Tabla de comisiones por producto y rango, montos acordados (negociación) y ranking de vendedores.
// Configurar y acordar: Administración/Contabilidad (comisiones.configurar). Ranking: equipo de ventas.
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/comisiones-tabla', [ComisionTablaController::class, 'index'])->middleware('permiso:comisiones.ver');
    Route::post('/comisiones-tabla/productos', [ComisionTablaController::class, 'guardarProducto'])->middleware('permiso:comisiones.configurar');
    Route::put('/comisiones-tabla/productos/{id}', [ComisionTablaController::class, 'guardarProducto'])->whereNumber('id')->middleware('permiso:comisiones.configurar');
    Route::patch('/comisiones-tabla/programas', [ComisionTablaController::class, 'asignarProgramas'])->middleware('permiso:comisiones.configurar');

    Route::get('/comisiones-ventas', [ComisionTablaController::class, 'ventas'])->middleware('permiso:comisiones.ver');
    Route::post('/comisiones-acuerdos', [ComisionTablaController::class, 'acordar'])->middleware('permiso:comisiones.configurar');
    Route::delete('/comisiones-acuerdos/{idIns}', [ComisionTablaController::class, 'quitarAcuerdo'])->whereNumber('idIns')->middleware('permiso:comisiones.configurar');

    Route::get('/ventas-ranking', [ComisionTablaController::class, 'ranking']);
});
