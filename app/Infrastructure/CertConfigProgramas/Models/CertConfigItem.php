<?php

namespace App\Infrastructure\CertConfigProgramas\Models;

use Illuminate\Database\Eloquent\Model;

class CertConfigItem extends Model
{
    protected $table = 'web_cert_config_item';

    protected $fillable = [
        'config_id',
        'plantilla_id',
        'nombre_cert',
        'precio',
        'es_gratuito',
        'orden',
        'activo',
    ];

    public $timestamps = false;
}
