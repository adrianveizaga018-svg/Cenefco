<?php

namespace App\Infrastructure\EnviosCertificado\Models;

use Illuminate\Database\Eloquent\Model;

class EnvioCertificado extends Model
{
    protected $table = 'envios_certificado';

    protected $fillable = [
        'id_ins',
        'ciudad_destino',
        'fecha_envio',
        'imagen_guia',
        'aclaraciones',
        'costo',
        'id_us_reg',
    ];

    protected $casts = [
        'id_ins'      => 'integer',
        'fecha_envio' => 'date',
        'costo'       => 'decimal:2',
        'id_us_reg'   => 'integer',
    ];
}
