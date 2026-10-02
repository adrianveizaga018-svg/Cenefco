<?php

namespace App\Infrastructure\Comisiones\Models;

use Illuminate\Database\Eloquent\Model;

class ComisionLiquidacion extends Model
{
    protected $table = 'comisiones_liquidacion';

    protected $fillable = [
        'vendedor_id',
        'fecha_desde',
        'fecha_hasta',
        'total_base',
        'porcentaje_comision',
        'total_inscritos',
        'monto_comision',
        'estado',
        'aprobado_por',
        'aprobado_at',
        'pagado_por',
        'pagado_at',
        'comprobante_pago_url',
        'nota',
    ];

    protected $casts = [
        'fecha_desde'         => 'date',
        'fecha_hasta'         => 'date',
        'total_base'          => 'decimal:2',
        'porcentaje_comision' => 'decimal:2',
        'total_inscritos'     => 'integer',
        'monto_comision'      => 'decimal:2',
        'aprobado_at'         => 'datetime',
        'pagado_at'           => 'datetime',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function detalle()
    {
        return $this->hasMany(ComisionLiquidacionDetalle::class, 'comisiones_liquidacion_id');
    }
}
