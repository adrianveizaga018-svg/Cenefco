<?php

namespace App\Infrastructure\Intents\Models;

use Illuminate\Database\Eloquent\Model;

class Intent extends Model
{
    protected $table = 'web_intents';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nombre', 'slug', 'dominio', 'prioridad', 'accion', 'activo', 'orden',
        'eventos', 'input_contexts', 'output_contexts', 'frases_entrenamiento', 'respuestas',
    ];

    protected $casts = [
        'activo'               => 'boolean',
        'eventos'              => 'array',
        'input_contexts'       => 'array',
        'output_contexts'      => 'array',
        'frases_entrenamiento' => 'array',
        'respuestas'           => 'array',
    ];
}
