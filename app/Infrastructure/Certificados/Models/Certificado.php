<?php

namespace App\Infrastructure\Certificados\Models;

use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    protected $table = 't_certificado';

    protected $fillable = [
        'lista_aprobado_id',
        'plantilla_id',
        'usuario_id',
        'imparte_id',
        'nombre_en_certificado',
        'programa_en_certificado',
        'condicion',
        'nota_final',
        'horas_academicas',
        'fecha_inicio_curso',
        'fecha_fin_curso',
        'codigo_verificacion',
        'qr_url',
        'archivo_url',
        'archivo_miniatura_url',
        'estado',
        'motivo_anulacion',
        'anulado_por',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;
}
