<?php

namespace App\Infrastructure\Carreras\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table = 't_carrera';
    protected $primaryKey = 'id_carrera';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_carrera',
        'id_us_reg',
        'num_carrera',
        'nombre_carrera',
        'estado',
        'fecha_reg',
    ];
}
