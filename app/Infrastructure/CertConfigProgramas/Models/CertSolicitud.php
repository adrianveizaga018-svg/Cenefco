<?php

namespace App\Infrastructure\CertConfigProgramas\Models;

use Illuminate\Database\Eloquent\Model;

class CertSolicitud extends Model
{
    protected $table = 'web_cert_solicitud';

    protected $fillable = [
        'config_item_id',
        'inscripcion_id',
        'usuario_ci',
        'usuario_nombre',
        'usuario_email',
        'es_gratuito',
        'estado',
        'comprobante_url',
        'monto_pagado',
        'nota_admin',
        'certificado_id',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;
}
