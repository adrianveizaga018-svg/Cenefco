<?php

namespace App\Infrastructure\CifrasInstitucionales\Models;

use Illuminate\Database\Eloquent\Model;

class CifraInstitucional extends Model
{
    protected $table = 'web_cifra_institucional';

    protected $fillable = [
        'valor',
        'etiqueta',
        'descripcion',
        'icono',
        'color',
        'orden',
        'activo',
    ];

    protected $casts = [
        'orden'  => 'integer',
        'activo' => 'boolean',
    ];
}
