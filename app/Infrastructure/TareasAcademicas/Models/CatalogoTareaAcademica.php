<?php

namespace App\Infrastructure\TareasAcademicas\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoTareaAcademica extends Model
{
    protected $table = 'catalogo_tareas_academicas';

    protected $fillable = [
        'titulo',
        'requiere_archivo',
        'estado',
    ];

    protected $casts = [
        'requiere_archivo' => 'boolean',
        'estado' => 'boolean',
    ];
}
