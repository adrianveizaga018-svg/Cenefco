<?php

namespace App\Infrastructure\RedesSociales\Models;

use Illuminate\Database\Eloquent\Model;

class RedSocial extends Model
{
    protected $table = 'web_redes_sociales';

    const UPDATED_AT = 'updated_at';
    const CREATED_AT = null;

    protected $fillable = [
        'red',
        'nombre_display',
        'url',
        'icono_clase',
        'pixel_id',
        'mostrar_footer',
        'mostrar_header',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo'         => 'boolean',
        'mostrar_footer' => 'boolean',
        'mostrar_header' => 'boolean',
        'orden'          => 'integer',
    ];
}
