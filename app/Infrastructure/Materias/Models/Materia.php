<?php

namespace App\Infrastructure\Materias\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 't_materia';
    protected $primaryKey = 'id_mat';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_mat',
        'id_us_reg',
        'sigla',
        'nombremat',
        'nombre',
        'semestre',
        'modalidad',
        'carga_horaria',
        'observacion',
        'estado',
        'fecha_reg',
    ];
}
