<?php

namespace App\Infrastructure\HistoriaMunicipio\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriaMunicipio extends Model
{
    protected $table = 'historia_municipio';

    public $timestamps = false;

    protected $fillable = [
        'titulo', 'contenido', 'fecha_inicio', 'fecha_fin',
        'imagen_url', 'orden', 'activo',
    ];

    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
        'created_at' => 'datetime',
    ];
}
