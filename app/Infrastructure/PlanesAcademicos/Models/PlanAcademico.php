<?php

namespace App\Infrastructure\PlanesAcademicos\Models;

use Illuminate\Database\Eloquent\Model;

class PlanAcademico extends Model
{
    protected $table = 't_plan';
    protected $primaryKey = 'id_plan';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_plan',
        'id_us_reg',
        'titulo',
        'titulo_plan',
        'convenio',
        'convenio_id',
        'anio',
        'numero_resolucion',
        'costo',
        'nro_cuotas',
        'descuento',
        'costo_por_cuota',
        'id_catplan',
        'estado',
        'fecha_reg',
    ];
}
