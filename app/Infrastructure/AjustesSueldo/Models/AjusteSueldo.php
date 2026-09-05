<?php

namespace App\Infrastructure\AjustesSueldo\Models;

use App\Infrastructure\Empleados\Models\Empleado;
use Illuminate\Database\Eloquent\Model;

class AjusteSueldo extends Model
{
    protected $table = 'ajuste_sueldo_empleado';

    protected $fillable = [
        'empleado_id',
        'anio',
        'mes',
        'tipo',
        'monto',
        'motivo',
        'aplicado',
        'planilla_detalle_id',
        'registrado_por',
    ];

    protected $casts = [
        'monto'    => 'decimal:2',
        'aplicado' => 'boolean',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}
