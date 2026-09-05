<?php

namespace App\Infrastructure\HitosInstitucionales\Models;

use Illuminate\Database\Eloquent\Model;

class HitoInstitucional extends Model
{
    protected $table = 'web_hito_institucional';

    protected $fillable = [
        'anio',
        'titulo',
        'descripcion',
        'imagen_url',
        'imagen_alt',
        'orden',
        'activo',
    ];

    protected $casts = [
        'orden'  => 'integer',
        'activo' => 'boolean',
    ];
}
