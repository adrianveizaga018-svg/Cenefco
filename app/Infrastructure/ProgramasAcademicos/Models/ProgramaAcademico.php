<?php

namespace App\Infrastructure\ProgramasAcademicos\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramaAcademico extends Model
{
    protected $table = 't_programa';
    protected $primaryKey = 'id_programa';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_programa',
        'id_us_reg',
        'num_programa',
        'nombre_programa',
        'descripcion',
        'foto',
        'inicio_actividades',
        'finalizacion_actividades',
        'inicio_inscripciones',
        'titulo_documento1',
        'documento1',
        'titulo_documento2',
        'documento2',
        'titulo_documento3',
        'documento3',
        'titulo_documento4',
        'documento4',
        'dirigido',
        'inversion',
        'requisitos',
        'creditaje',
        'objetivo',
        'nota',
        'id_tipoprograma',
        'url_video',
        'estado',
        'fecha_reg',
    ];
}
