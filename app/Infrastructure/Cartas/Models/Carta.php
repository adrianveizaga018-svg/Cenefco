<?php

namespace App\Infrastructure\Cartas\Models;

use Illuminate\Database\Eloquent\Model;

class Carta extends Model
{
    protected $table      = 't_carta';
    protected $primaryKey = 'id_carta';
    public    $timestamps = false;
    public    $incrementing = false;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_carta', 'id_us_reg', 'num_carta', 'id_us', 'id_plan',
        'nombresenor', 'nombretitulo',
        'textocarta1', 'textocarta2', 'textocarta3',
        'estado', 'fecha_reg',
    ];
}
