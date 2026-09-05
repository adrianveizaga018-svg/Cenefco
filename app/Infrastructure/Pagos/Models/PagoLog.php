<?php

namespace App\Infrastructure\Pagos\Models;

use Illuminate\Database\Eloquent\Model;

class PagoLog extends Model
{
    protected $table      = 't_pagolog';
    protected $primaryKey = 'id_pagolog';
    public    $timestamps = false;

    protected $fillable = [
        'id_pago', 'tipo_log', 'id_us_reg', 'num_pago',
        'id_us', 'id_mat', 'id_fechapago',
        'monto_pagado', 'monto_pago_extra', 'nro_boleta_bancaria',
        'fecha_deposito', 'nro_nit', 'nombre_nit',
        'tipo_fechapago', 'observacion_pago', 'pago_extra',
        'estado', 'fecha_reg', 'monto_descuento_extra', 'motivo_descuento',
    ];
}
