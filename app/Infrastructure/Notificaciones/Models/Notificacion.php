<?php

namespace App\Infrastructure\Notificaciones\Models;

use App\Enums\DestinatarioEnum;
use App\Enums\PrioridadEnum;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        't_usuario_id',
        'tipo',
        'titulo',
        'mensaje',
        'url_accion',
        'leida',
        'leida_at',
        'canal',
        'enviada',
        'enviada_at',
        'prioridad',
        'destinatario',
        'rol_destino',
        'icono',
        'color',
        'imagen_url',
        'imagen_nombre',
        'expires_at',
        'created_at',
    ];

    protected $casts = [
        'leida'        => 'boolean',
        'enviada'      => 'boolean',
        'leida_at'     => 'datetime',
        'enviada_at'   => 'datetime',
        'expires_at'   => 'datetime',
        'created_at'   => 'datetime',
        'prioridad'    => PrioridadEnum::class,
        'destinatario' => DestinatarioEnum::class,
    ];
}
