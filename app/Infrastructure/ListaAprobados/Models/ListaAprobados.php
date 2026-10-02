<?php

namespace App\Infrastructure\ListaAprobados\Models;

use Illuminate\Database\Eloquent\Model;

class ListaAprobados extends Model
{
    protected $table = 't_lista_aprobados';

    protected $fillable = [
        'imparte_id',
        'usuario_id',
        'inscripcion_id',
        'nota_final',
        'nota_minima',
        'condicion',
        'observacion',
        'comprobante_url',
        'ajuste_manual',
        'estado_certificado',
        'notificado_email',
        'registrado_por',
        'id_us_reg',
        'created_at',
        'updated_at',
    ];

    public $timestamps = false;
}
