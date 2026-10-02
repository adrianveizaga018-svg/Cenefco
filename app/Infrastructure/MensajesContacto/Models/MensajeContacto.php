<?php

namespace App\Infrastructure\MensajesContacto\Models;

use Illuminate\Database\Eloquent\Model;

class MensajeContacto extends Model
{
    protected $table = 'mensajes_contacto';

    public $timestamps = false;

    protected $fillable = [
        'secretaria_destino_id',
        'nombre_remitente',
        'email_remitente',
        'telefono_remitente',
        'asunto',
        'mensaje',
        'estado',
        'respuesta',
        'respondido_por',
        'respondido_at',
        'ip_origen',
    ];

    protected $casts = [
        'secretaria_destino_id' => 'integer',
        'respondido_por' => 'integer',
        'respondido_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
