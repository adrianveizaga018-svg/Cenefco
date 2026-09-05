<?php

namespace App\Infrastructure\CategoriasCampo\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaCampo extends Model
{
    protected $table = 'web_categoria_campo';

    protected $fillable = [
        'categoria_id',
        'nombre_campo',
        'etiqueta',
        'tipo_campo',
        'requerido',
        'orden',
        'paso',
        'activo',
        'ayuda',
        'opciones',
        'validacion',
    ];

    protected $casts = [
        'categoria_id' => 'integer',
        'requerido'    => 'boolean',
        'orden'        => 'integer',
        'paso'         => 'integer',
        'activo'       => 'boolean',
        'opciones'     => 'array',
        'validacion'   => 'array',
    ];
}
