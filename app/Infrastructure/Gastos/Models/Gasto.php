<?php

namespace App\Infrastructure\Gastos\Models;

use App\Infrastructure\CampanasPublicidad\Models\CampanaPublicidad;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gasto extends Model
{
    use SoftDeletes;

    protected $table = 'gasto';

    protected $fillable = [
        'categoria_gasto_id',
        'concepto',
        'monto',
        'fecha',
        'responsable',
        'comprobante_url',
        'nota',
        'gasto_recurrente_id',
        'campana_publicidad_id',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'date',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function categoria()
    {
        return $this->belongsTo(CategoriaGasto::class, 'categoria_gasto_id');
    }

    public function gastoRecurrente()
    {
        return $this->belongsTo(GastoRecurrente::class, 'gasto_recurrente_id');
    }

    public function campanaPublicidad()
    {
        return $this->belongsTo(CampanaPublicidad::class, 'campana_publicidad_id');
    }
}
