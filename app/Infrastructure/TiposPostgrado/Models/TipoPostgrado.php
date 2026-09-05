<?php

namespace App\Infrastructure\TiposPostgrado\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPostgrado extends Model
{
    protected $table = 't_tipopostgrado';
    protected $primaryKey = 'id_tipopost';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_tipopost', 'id_plan', 'id_us_reg', 'num_tipopost',
        'id_tipopago', 'descuentopostgrado', 'calculo_cuota', 'estado', 'fecha_reg',
    ];
}
