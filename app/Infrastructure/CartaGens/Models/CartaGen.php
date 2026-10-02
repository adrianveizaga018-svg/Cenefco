<?php

namespace App\Infrastructure\CartaGens\Models;

use Illuminate\Database\Eloquent\Model;

class CartaGen extends Model
{
    protected $table      = 't_cartagen';
    protected $primaryKey = 'id_cartagen';
    public    $timestamps = false;
    public    $incrementing = false;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_cartagen', 'id_us_reg', 'num_carta', 'id_us', 'id_cartamod',
        'textocarta', 'textocarta1', 'textocarta3',
        'usar_encabezado_pie_estandar',
        'cp_nro_contrato', 'cp_gestion_contrato',
        'estado', 'fecha_reg',
    ];
}
