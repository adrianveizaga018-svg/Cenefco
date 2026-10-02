<?php

namespace App\Infrastructure\Aliados\Models;

use Illuminate\Database\Eloquent\Model;

class Aliado extends Model
{
    protected $table = 'web_aliado';

    protected $fillable = [
        'nombre',
        'logo_url',
        'logo_alt',
        'url_sitio',
        'descripcion',
        'tipo',
        'orden',
        'activo',
    ];

    protected $casts = [
        'orden'  => 'integer',
        'activo' => 'boolean',
    ];
}
