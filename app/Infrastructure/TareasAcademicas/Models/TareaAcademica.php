<?php

namespace App\Infrastructure\TareasAcademicas\Models;

use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Cursos\Models\Curso;
use App\Infrastructure\Usuarios\Models\User;

class TareaAcademica extends Model
{
    protected $table = 'tareas_academicas';

    protected $fillable = [
        'programa_id',
        'titulo',
        'descripcion',
        'requiere_archivo',
        'estado',
        'archivo_url',
        'completado_por_usuario_id',
        'fecha_completado',
        'catalogo_id',
    ];

    protected $casts = [
        'requiere_archivo' => 'boolean',
        'fecha_completado' => 'datetime',
    ];

    public function programa()
    {
        return $this->belongsTo(Curso::class, 'programa_id', 'id_programa');
    }

    public function completadoPor()
    {
        return $this->belongsTo(User::class, 'completado_por_usuario_id', 'id');
    }
}
