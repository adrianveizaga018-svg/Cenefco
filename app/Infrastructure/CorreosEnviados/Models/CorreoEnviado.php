<?php

namespace App\Infrastructure\CorreosEnviados\Models;

use Illuminate\Database\Eloquent\Model;

class CorreoEnviado extends Model
{
    protected $table = 'web_correo_enviado';

    protected $fillable = [
        'tipo', 'destinatario', 'asunto',
        'referencia_tipo', 'referencia_id',
        'estado', 'error', 'enviado_por',
    ];
}
