<?php

namespace App\Infrastructure\CertPlantillas\Models;

use Illuminate\Database\Eloquent\Model;

class CertPlantilla extends Model
{
    protected $table = 't_cert_plantilla';

    protected $fillable = [
        'nombre',
        'tipo',
        'imagen_url',
        'ancho_px',
        'alto_px',
        'orientacion',
        'formato_salida',
        'calidad_jpg',
        'fuente_default',
        'color_default',
        'estado',
        'notas',
        'id_us_reg',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;
}
