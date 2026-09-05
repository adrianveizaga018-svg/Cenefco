<?php

namespace App\Infrastructure\CartaModelos\Models;

use Illuminate\Database\Eloquent\Model;

class CartaModelo extends Model
{
    protected $table      = 't_cartamodelo';
    protected $primaryKey = 'id_cartamod';
    public    $timestamps = false;
    public    $incrementing = false;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_cartamod', 'id_us_reg', 'num_cartamod', 'nombremodelo',
        'textocarta', 'textocarta1', 'textocarta3', 'texto_carta',
        'usar_encabezado_pie_estandar', 'estado', 'fecha_reg',
    ];
}
