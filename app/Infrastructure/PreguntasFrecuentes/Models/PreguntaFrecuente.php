<?php

namespace App\Infrastructure\PreguntasFrecuentes\Models;

use Illuminate\Database\Eloquent\Model;

class PreguntaFrecuente extends Model
{
    protected $table = 'web_faq';

    public $timestamps = false;

    protected $fillable = [
        'pregunta',
        'respuesta',
        'categoria',
        'orden',
        'activo',
    ];

    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
        'created_at' => 'datetime',
    ];
}
