<?php

namespace App\Infrastructure\Ayudas\Models;

use Illuminate\Database\Eloquent\Model;

class Ayuda extends Model
{
    protected $table      = 't_ayuda';
    protected $primaryKey = 'id_ayuda';
    public    $timestamps = false;
    public    $incrementing = false;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_ayuda', 'id_us_reg', 'num_ayuda', 'id_us',
        'gestion', 'monto_pagado', 'nro_recibo', 'fecha_recibo',
        'observacion_pago', 'id_categoriatipoayuda', 'estado', 'fecha_reg',
    ];
}
