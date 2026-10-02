<?php

namespace App\Infrastructure\DescuentosPromociones\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DescuentoPromocion extends Model
{
    use SoftDeletes;

    protected $table = 'web_descuento_promocion';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'tipo_descuento',
        'valor',
        'monto_minimo',
        'usos_maximos',
        'usos_actuales',
        'usos_por_usuario',
        'programa_id',
        'activo',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'valor'            => 'decimal:2',
        'monto_minimo'     => 'float',
        'usos_maximos'     => 'integer',
        'usos_actuales'    => 'integer',
        'usos_por_usuario' => 'integer',
        'programa_id'      => 'integer',
        'activo'           => 'boolean',
        'fecha_inicio'     => 'datetime',
        'fecha_fin'        => 'datetime',
    ];
}
