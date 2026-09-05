<?php

namespace App\Infrastructure\Convenios\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Convenio extends Model
{
    use SoftDeletes;

    protected $table = 'web_convenio';

    protected $fillable = [
        'nombre',
        'institucion',
        'tipo',
        'descripcion',
        'responsable',
        'contacto_email',
        'contacto_telefono',
        'fecha_inicio',
        'fecha_fin',
        'documento_url',
        'logo_url',
        'estado',
        'orden',
    ];

    protected $casts = [
        'orden'       => 'integer',
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];
}
