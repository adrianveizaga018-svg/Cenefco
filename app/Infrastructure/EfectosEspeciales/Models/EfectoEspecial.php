<?php

namespace App\Infrastructure\EfectosEspeciales\Models;

use Illuminate\Database\Eloquent\Model;

class EfectoEspecial extends Model
{
    protected $table = 'efectos_especiales';

    protected $fillable = [
        'nombre',
        'tipo_efecto',
        'color_primario',
        'color_secundario',
        'fecha_inicio',
        'fecha_fin',
        'intensidad',
        'activo',
    ];

    protected $casts = [
        'activo'     => 'boolean',
        'intensidad' => 'integer',
    ];
}
