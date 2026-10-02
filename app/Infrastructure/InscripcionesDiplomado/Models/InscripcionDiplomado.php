<?php

namespace App\Infrastructure\InscripcionesDiplomado\Models;

use Illuminate\Database\Eloquent\Model;

class InscripcionDiplomado extends Model
{
    protected $table = 'web_inscripcion_diplomado';

    protected $fillable = [
        'programa_id', 'nombre', 'apellido_paterno', 'apellido_materno', 'fecha_nacimiento', 'email', 'ci', 'expedido_id',
        'telefono_grupo_inscritos', 'archivo_ci', 'archivo_titulo', 'archivo_cv', 'archivo_foto_3x3',
        'ciudad_residencia_id', 'provincia_especificar', 'medio_pago', 'medio_pago_id', 'monto_pagado',
        'archivo_comprobante_pago',
        'sugerencia_curso', 'recomendar_docente', 'detalle_docente',
        'estado', 'notificado', 'fecha_notificacion', 'origen', 'ip_origen',
    ];
}
