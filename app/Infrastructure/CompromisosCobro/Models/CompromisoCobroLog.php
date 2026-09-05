<?php

namespace App\Infrastructure\CompromisosCobro\Models;

use Illuminate\Database\Eloquent\Model;

class CompromisoCobroLog extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'web_compromiso_cobro_log';

    protected $fillable = [
        'compromiso_cobro_id', 'tipo_evento', 'fecha_anterior', 'fecha_nueva',
        'motivo', 'observacion', 'registrado_por',
    ];
}
