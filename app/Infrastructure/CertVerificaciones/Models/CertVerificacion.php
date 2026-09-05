<?php

namespace App\Infrastructure\CertVerificaciones\Models;

use Illuminate\Database\Eloquent\Model;

class CertVerificacion extends Model
{
    protected $table = 't_cert_verificacion';

    protected $fillable = [
        'certificado_id',
        'codigo_consultado',
        'resultado',
        'ip_origen',
        'user_agent',
        'pais',
        'created_at',
    ];

    public $timestamps = false;
}
