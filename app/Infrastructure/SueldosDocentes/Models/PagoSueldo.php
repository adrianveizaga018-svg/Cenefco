<?php

namespace App\Infrastructure\SueldosDocentes\Models;

use Illuminate\Database\Eloquent\Model;

class PagoSueldo extends Model
{
    protected $table = 'web_pago_sueldo';

    protected $fillable = [
        'id_sueldo', 'monto_pagado', 'fecha_pago',
        'nro_comprobante', 'observacion', 'comprobante_archivo', 'estado',
    ];

    protected $casts = [
        'monto_pagado' => 'float',
    ];
}
