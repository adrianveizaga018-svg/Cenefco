<?php

namespace App\Infrastructure\Planillas\Models;

use Illuminate\Database\Eloquent\Model;

class Planilla extends Model
{
    protected $table = 'planilla';

    protected $fillable = [
        'anio',
        'mes',
        'total',
        'gasto_id',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function detalle()
    {
        return $this->hasMany(PlanillaDetalle::class, 'planilla_id');
    }
}
