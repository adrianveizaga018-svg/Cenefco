<?php

namespace App\Infrastructure\TiposBanco\Models;

use Illuminate\Database\Eloquent\Model;

class TipoBanco extends Model
{
    protected $table = 'tipos_banco';

    protected $fillable = [
        'nombre',
        'activo',
        'orden',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden'  => 'integer',
    ];
}
