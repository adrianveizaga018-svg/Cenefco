<?php

namespace App\Infrastructure\GradosAcademicos\Models;

use Illuminate\Database\Eloquent\Model;

class GradoAcademico extends Model
{
    protected $table      = 'web_grado_academico';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'nombre', 'abreviatura', 'requiere_titulo', 'orden', 'activo',
        'created_at', 'updated_at',
    ];
}
