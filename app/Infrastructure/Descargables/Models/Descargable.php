<?php

declare(strict_types=1);

namespace App\Infrastructure\Descargables\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Descargable extends Model
{
    use SoftDeletes;

    protected $table = 'web_descargable';

    protected $fillable = [
        'nombre',
        'tipo',
        'archivo_url',
        'imagen_portada_url',
        'programa_id',
        'requiere_datos',
        'descargas',
        'orden',
        'activo',
    ];

    protected $casts = [
        'programa_id'   => 'integer',
        'requiere_datos' => 'boolean',
        'descargas'     => 'integer',
        'orden'         => 'integer',
        'activo'        => 'boolean',
    ];
}
