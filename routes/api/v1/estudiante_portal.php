<?php

use App\Http\Controllers\Api\EstudiantePortalController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('estudiante')->group(function () {
    Route::get('/dashboard', [EstudiantePortalController::class, 'dashboard']);
    Route::get('/mis-cursos', [EstudiantePortalController::class, 'misCursos']);
    Route::get('/mis-certificados', [EstudiantePortalController::class, 'misCertificados']);
    Route::get('/mis-pagos', [EstudiantePortalController::class, 'misPagos']);
});
