<?php

namespace App\Infrastructure\MdlCourses\Models;

use Illuminate\Database\Eloquent\Model;

class MdlCourse extends Model
{
    protected $table = 'mdl_course';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id', 'id_us_reg', 'num_modcurso', 'fullname', 'shortname',
        'id_docente', 'category', 'sigla', 'paralelo', 'cupo',
        'grado_academico1', 'grado_academico2', 'observacion_imp',
        'imparte_fecha_inicio', 'imparte_fecha_fin', 'imparte_fecha_acta',
        'ocultar_coordinador_acta', 'id_coordinador',
        'grado_coordinador1', 'grado_coordinador2',
        'titulo_personalizado', 'subtitulo_personalizado',
        'gestion', 'per_modificar', 'fecha_reg', 'estado',
    ];
}
