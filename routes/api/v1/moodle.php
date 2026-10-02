<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('moodle')->group(function () {
    Route::get('/courses', [\App\Http\Controllers\Api\MoodleCourseController::class, 'index'])
        ->middleware('permiso:moodle.ver');
    Route::post('/courses', [\App\Http\Controllers\Api\MoodleCourseController::class, 'store'])
        ->middleware('permiso:moodle.crear');
    Route::post('/courses/from-curso/{id}', [\App\Http\Controllers\Api\MoodleCourseController::class, 'fromCurso'])
        ->middleware('permiso:moodle.crear');
});
