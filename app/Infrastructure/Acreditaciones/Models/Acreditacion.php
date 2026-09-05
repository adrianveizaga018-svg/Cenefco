<?php

namespace App\Infrastructure\Acreditaciones\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Acreditacion extends Model
{
    use SoftDeletes;

    protected $table = 'web_acreditacion';

    protected $fillable = [
        'nombre',
        'entidad_otorgante',
        'tipo',
        'descripcion',
        'logo_url',
        'logo_alt',
        'url_verificacion',
        'fecha_obtencion',
        'fecha_vencimiento',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo'            => 'boolean',
        'orden'             => 'integer',
        'fecha_obtencion'   => 'date:Y-m-d',
        'fecha_vencimiento' => 'date:Y-m-d',
    ];
}
