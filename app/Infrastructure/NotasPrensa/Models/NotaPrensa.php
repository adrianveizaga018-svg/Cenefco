<?php

declare(strict_types=1);

namespace App\Infrastructure\NotasPrensa\Models;

use Illuminate\Database\Eloquent\Model;

class NotaPrensa extends Model
{
    protected $table = 'web_nota_prensa';

    protected $fillable = [
        'titulo',
        'medio',
        'logo_medio_url',
        'logo_medio_alt',
        'resumen',
        'url_noticia',
        'fecha_publicacion',
        'destacada',
        'orden',
        'activo',
    ];

    protected $casts = [
        'destacada'         => 'boolean',
        'orden'             => 'integer',
        'activo'            => 'boolean',
        'fecha_publicacion' => 'date',
    ];
}
