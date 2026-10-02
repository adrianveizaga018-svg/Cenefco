<?php

namespace App\Infrastructure\Planillas\Models;

use Illuminate\Database\Eloquent\Model;

class PlanillaDetalle extends Model
{
    public $timestamps = false;

    protected $table = 'planilla_detalle';

    protected $fillable = [
        'planilla_id',
        'empleado_id',
        'nombre_completo',
        'cargo',
        'monto',
        'monto_base',
        'total_descuentos',
        'total_bonos',
    ];

    protected $casts = [
        'monto'            => 'decimal:2',
        'monto_base'       => 'decimal:2',
        'total_descuentos' => 'decimal:2',
        'total_bonos'      => 'decimal:2',
    ];

    public function planilla()
    {
        return $this->belongsTo(Planilla::class, 'planilla_id');
    }
}
