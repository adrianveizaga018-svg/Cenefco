<?php

namespace App\Infrastructure\Pagos\Models;

use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    protected $table = 'web_devolucion';

    protected $fillable = [
        'id_ins', 'id_us', 'monto', 'motivo',
        'documento_url', 'estado', 'nota_respuesta', 'fecha_respuesta',
    ];

    protected $casts = [
        'monto' => 'float',
    ];
}
