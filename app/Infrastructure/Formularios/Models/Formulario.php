<?php

namespace App\Infrastructure\Formularios\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Formulario extends Model
{
    use SoftDeletes;

    protected $table = 'web_formulario';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'campos',
        'activo',
    ];

    protected $casts = [
        'campos' => 'array',
        'activo' => 'boolean',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
