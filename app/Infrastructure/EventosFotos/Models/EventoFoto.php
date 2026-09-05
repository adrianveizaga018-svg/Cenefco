<?php

namespace App\Infrastructure\EventosFotos\Models;

use Illuminate\Database\Eloquent\Model;

class EventoFoto extends Model
{
    protected $table = 'eventos_fotos';

    public $timestamps = false;

    protected $fillable = [
        'evento_id',
        'archivo_url',
        'titulo',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];
}
