<?php

namespace App\Infrastructure\Resenas\Models;

use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    protected $table = 'web_programa_resena';

    protected $fillable = [
        'programa_id', 'usuario_id', 'nombre', 'cargo_actual', 'foto_url',
        'calificacion', 'titulo_resena', 'resena', 'estado', 'verificado',
        'destacada', 'motivo_rechazo',
    ];

    protected $casts = [
        'calificacion' => 'integer',
        'verificado'   => 'boolean',
        'destacada'    => 'boolean',
    ];
}
