<?php

namespace App\Infrastructure\CompromisosCobro\Models;

use Illuminate\Database\Eloquent\Model;

class CompromisoCobro extends Model
{
    protected $table = 'web_compromiso_cobro';

    protected $fillable = [
        'id_ins', 'id_us', 'id_imp', 'fecha_compromiso', 'hora_compromiso', 'monto_comprometido',
        'observacion', 'estado', 'veces_reprogramado', 'registrado_por',
        'notificado_at', 'vencido_notificado_at',
    ];

    protected $casts = [
        'monto_comprometido'     => 'float',
        'veces_reprogramado'     => 'integer',
        'fecha_compromiso'       => 'date:Y-m-d',
        'notificado_at'          => 'datetime',
        'vencido_notificado_at'  => 'datetime',
    ];
}
