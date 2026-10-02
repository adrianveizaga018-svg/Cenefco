<?php

namespace App\Infrastructure\ConfigSitio\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigSitio extends Model
{
    protected $table = 'config_sitio';

    protected $fillable = [
        'nombre',
        'slogan',
        'descripcion',
        'logo_url',
        'favicon_url',
        'email_contacto',
        'telefono',
        'direccion',
        'ciudad',
        'pais',
        'latitud',
        'longitud',
        'horario_atencion',
        'whatsapp_numero',
        'whatsapp_mensaje',
        'meta_titulo',
        'meta_descripcion',
        'meta_keywords',
        'google_analytics_id',
        'activo',
    ];

    protected $casts = [
        'latitud'  => 'float',
        'longitud' => 'float',
        'activo'   => 'boolean',
    ];
}
