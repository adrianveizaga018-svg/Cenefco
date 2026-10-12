<?php

use App\Http\Controllers\Api\CrmController;
use Illuminate\Support\Facades\Route;

// Embudo de ventas: el vendedor trabaja lo suyo (crm.ver); Dirección ve todo (crm.supervisar);
// marketing sube contactos masivos (crm.importar)
Route::middleware(['auth:sanctum'])->prefix('crm')->group(function () {
    Route::get('/leads', [CrmController::class, 'leads'])->middleware('permiso:crm.ver');
    Route::post('/leads', [CrmController::class, 'store'])->middleware('permiso:crm.ver');
    Route::post('/leads/contacto-masivo', [CrmController::class, 'contactoMasivo'])->middleware('permiso:crm.ver');
    Route::patch('/leads/asignar-masivo', [CrmController::class, 'asignarMasivo'])->middleware('permiso:crm.supervisar');
    Route::get('/leads/{id}', [CrmController::class, 'show'])->whereNumber('id')->middleware('permiso:crm.ver');
    Route::post('/leads/{id}/contacto', [CrmController::class, 'contacto'])->whereNumber('id')->middleware('permiso:crm.ver');
    Route::patch('/leads/{id}/etapa', [CrmController::class, 'etapa'])->whereNumber('id')->middleware('permiso:crm.ver');
    Route::patch('/leads/{id}/asignar', [CrmController::class, 'asignar'])->whereNumber('id')->middleware('permiso:crm.supervisar');
    Route::get('/tareas', [CrmController::class, 'tareas'])->middleware('permiso:crm.ver');
    Route::get('/tablero', [CrmController::class, 'tablero'])->middleware('permiso:crm.ver');
    Route::get('/vendedores', [CrmController::class, 'vendedores'])->middleware('permiso:crm.ver');
    Route::post('/importar', [CrmController::class, 'importar'])->middleware(['permiso:crm.importar', 'throttle:10,1']);
    Route::get('/importar/plantilla', [CrmController::class, 'plantilla'])->middleware('permiso:crm.importar');
});
