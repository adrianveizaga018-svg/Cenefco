<?php

use App\Http\Controllers\Api\CrmController;
use Illuminate\Support\Facades\Route;

// Embudo de ventas: el vendedor trabaja lo suyo (crm.ver); Dirección ve todo (crm.supervisar)
Route::middleware(['auth:sanctum'])->prefix('crm')->group(function () {
    Route::get('/leads', [CrmController::class, 'leads'])->middleware('permiso:crm.ver');
    Route::post('/leads', [CrmController::class, 'store'])->middleware('permiso:crm.ver');
    Route::get('/leads/{id}', [CrmController::class, 'show'])->whereNumber('id')->middleware('permiso:crm.ver');
    Route::post('/leads/{id}/contacto', [CrmController::class, 'contacto'])->whereNumber('id')->middleware('permiso:crm.ver');
    Route::patch('/leads/{id}/etapa', [CrmController::class, 'etapa'])->whereNumber('id')->middleware('permiso:crm.ver');
    Route::patch('/leads/{id}/asignar', [CrmController::class, 'asignar'])->whereNumber('id')->middleware('permiso:crm.supervisar');
    Route::get('/tablero', [CrmController::class, 'tablero'])->middleware('permiso:crm.ver');
    Route::get('/vendedores', [CrmController::class, 'vendedores'])->middleware('permiso:crm.ver');
});
