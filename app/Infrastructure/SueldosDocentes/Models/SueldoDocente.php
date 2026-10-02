<?php

namespace App\Infrastructure\SueldosDocentes\Models;

use Illuminate\Database\Eloquent\Model;

class SueldoDocente extends Model
{
    protected $table = 'web_sueldo_docente';

    protected $fillable = [
        'id_us', 'id_imp', 'id_programa', 'concepto', 'periodo', 'gestion',
        'monto_total', 'observacion', 'archivo_pdf', 'estado',
    ];

    protected $casts = [
        'monto_total' => 'float',
        'gestion'     => 'integer',
    ];
}
