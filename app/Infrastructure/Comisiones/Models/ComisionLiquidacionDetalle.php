<?php

namespace App\Infrastructure\Comisiones\Models;

use App\Infrastructure\CategoriasPrograma\Models\CategoriaPrograma;
use Illuminate\Database\Eloquent\Model;

class ComisionLiquidacionDetalle extends Model
{
    protected $table = 'comisiones_liquidacion_detalle';

    public $timestamps = false;

    protected $appends = ['categoria_nombre'];

    protected $fillable = [
        'comisiones_liquidacion_id',
        'id_pago',
        'id_ins',
        'categoria_id',
        'comision_monto',
        'monto_pagado',
        'fecha_deposito',
    ];

    protected $casts = [
        'comision_monto' => 'decimal:2',
        'monto_pagado'   => 'decimal:2',
        'fecha_deposito' => 'date',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaPrograma::class, 'categoria_id');
    }

    public function getCategoriaNombreAttribute(): ?string
    {
        return $this->categoria?->nombre;
    }
}
