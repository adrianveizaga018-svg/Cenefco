<?php

namespace App\Infrastructure\Gastos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GastoRecurrente extends Model
{
    use SoftDeletes;

    protected $table = 'gasto_recurrente';

    protected $fillable = [
        'categoria_gasto_id',
        'concepto',
        'monto',
        'dia_del_mes',
        'activo',
        'ultima_confirmacion',
    ];

    protected $casts = [
        'monto'               => 'decimal:2',
        'activo'              => 'boolean',
        'ultima_confirmacion' => 'date',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function categoria()
    {
        return $this->belongsTo(CategoriaGasto::class, 'categoria_gasto_id');
    }
}
