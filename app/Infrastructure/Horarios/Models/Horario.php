<?php

namespace App\Infrastructure\Horarios\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 't_horario';
    protected $primaryKey = 'id_horar';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_horar',
        'id_us_reg',
        'id_imp',
        'id_d',
        'hora_inicio',
        'hora_fin',
        'periodos',
        'estado',
        'fecha_reg',
    ];
}
