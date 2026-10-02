<?php

namespace App\Infrastructure\Pagos\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table      = 't_pago';
    protected $primaryKey = 'id_pago';
    public    $timestamps = false;

    protected $fillable = [
        'id_us_reg', 'id_us', 'id_mat', 'id_fechapago', 'id_ins',
        'monto_pagado', 'nro_boleta_bancaria', 'fecha_deposito',
        'nro_nit', 'nombre_nit', 'tipo_fechapago',
        'observacion_pago', 'pago_extra', 'estado', 'fecha_reg',
        'metodo_pago', 'id_us_cajero', 'comprobante_archivo', 'estado_verificacion',
        'nota_verificacion', 'monto_descuento_extra', 'motivo_descuento', 'tipo_banco_id',
    ];

    protected $casts = [
        'monto_pagado'           => 'float',
        'monto_descuento_extra'  => 'float',
        'pago_extra'             => 'integer',
        'estado'                 => 'integer',
        'tipo_banco_id'          => 'integer',
    ];
}
