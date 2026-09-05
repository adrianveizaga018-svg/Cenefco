<?php

namespace App\Infrastructure\Pagos\Models;

use Illuminate\Database\Eloquent\Model;

class FechaPago extends Model
{
    protected $table      = 't_fechapago';
    protected $primaryKey = 'id_fechapago';
    public    $timestamps = false;

    protected $fillable = [
        'id_us_reg', 'id_plan', 'nro_pago', 'monto_a_pagar',
        'fecha_inicio', 'fecha_fin', 'obligatorio',
        'tipo_tramite', 'estado', 'fecha_reg',
    ];

    protected $casts = [
        'monto_a_pagar' => 'float',
        'obligatorio'   => 'integer',
        'estado'        => 'integer',
    ];
}
