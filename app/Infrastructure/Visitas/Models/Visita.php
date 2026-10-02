<?php

namespace App\Infrastructure\Visitas\Models;

use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    protected $table    = 'web_visitas';
    public    $timestamps = false;

    protected $fillable = [
        'session_id', 'url', 'ruta', 'titulo', 'referrer',
        'pais', 'ciudad', 'dispositivo', 'navegador', 'so', 'duracion_seg',
    ];

    protected $casts = [
        'duracion_seg' => 'integer',
        'created_at'   => 'datetime',
    ];
}
