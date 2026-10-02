<?php

namespace App\Infrastructure\Inscripciones\Models;

use Illuminate\Database\Eloquent\Model;

class InscripcionCuota extends Model
{
    protected $table = 't_inscripcion_cuotas';

    protected $fillable = [
        'id_ins',
        'id_fechapago_template',
        'nro_cuota',
        'descripcion',
        'monto_a_pagar',
        'fecha_vencimiento',
        'estado',
        'id_pago',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'monto_a_pagar' => 'float',
    ];
}
