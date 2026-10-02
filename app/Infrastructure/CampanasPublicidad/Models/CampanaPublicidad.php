<?php

namespace App\Infrastructure\CampanasPublicidad\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampanaPublicidad extends Model
{
    use SoftDeletes;

    protected $table = 'campana_publicidad';

    protected $fillable = [
        'programa_id',
        'proposito',
        'nombre',
        'plataforma',
        'objetivo',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'leads',
        'presupuesto_usd',
        'presupuesto_bob',
        'id_campana_externa',
        'responsable',
        'notas',
    ];

    protected $casts = [
        'fecha_inicio'    => 'date',
        'fecha_fin'       => 'date',
        'presupuesto_usd' => 'decimal:2',
        'presupuesto_bob' => 'decimal:2',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function metricas()
    {
        return $this->hasMany(CampanaMetrica::class, 'campana_publicidad_id')->orderByDesc('fecha_corte');
    }
}
