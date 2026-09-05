<?php

namespace App\Infrastructure\CampanasPublicidad\Models;

use Illuminate\Database\Eloquent\Model;

class CampanaMetrica extends Model
{
    protected $table = 'campana_metrica';

    public $timestamps = false;

    protected $fillable = [
        'campana_publicidad_id',
        'fecha_corte',
        'alcance',
        'impresiones',
        'frecuencia',
        'clics_enlace',
        'ctr',
        'cpc',
        'cpm',
        'resultados',
        'tipo_resultado',
        'costo_por_resultado',
        'gasto_periodo',
        'fuente',
        'notas',
    ];

    protected $casts = [
        'fecha_corte'          => 'date',
        'frecuencia'           => 'decimal:2',
        'ctr'                  => 'decimal:3',
        'cpc'                  => 'decimal:2',
        'cpm'                  => 'decimal:2',
        'costo_por_resultado'  => 'decimal:2',
        'gasto_periodo'        => 'decimal:2',
    ];

    const CREATED_AT = 'created_at';

    public function campana()
    {
        return $this->belongsTo(CampanaPublicidad::class, 'campana_publicidad_id');
    }
}
