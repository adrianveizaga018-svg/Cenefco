<?php

namespace App\Infrastructure\Redirecciones\Models;

use Illuminate\Database\Eloquent\Model;

class Redireccion extends Model
{
    protected $table = 'web_redireccion';

    protected $fillable = [
        'url_origen',
        'url_destino',
        'codigo_http',
        'hits',
        'activo',
        'notas',
    ];

    protected $casts = [
        'codigo_http' => 'integer',
        'hits'        => 'integer',
        'activo'      => 'boolean',
    ];
}
