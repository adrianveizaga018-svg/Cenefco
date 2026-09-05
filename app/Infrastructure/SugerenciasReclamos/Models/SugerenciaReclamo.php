<?php

namespace App\Infrastructure\SugerenciasReclamos\Models;

use Illuminate\Database\Eloquent\Model;

class SugerenciaReclamo extends Model
{
    protected $table = 'sugerencias';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'asunto',
        'mensaje',
        'email_respuesta',
        'secretaria_destino_id',
        'estado',
        'respuesta',
        'respondido_por',
        'respondido_at',
    ];

    protected $casts = [
        'usuario_id' => 'integer',
        'secretaria_destino_id' => 'integer',
        'respondido_por' => 'integer',
        'respondido_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
