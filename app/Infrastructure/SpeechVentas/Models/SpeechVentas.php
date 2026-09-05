<?php

namespace App\Infrastructure\SpeechVentas\Models;

use Illuminate\Database\Eloquent\Model;

class SpeechVentas extends Model
{
    protected $table = 'web_speech_ventas';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'titulo', 'categoria', 'contenido', 'palabras_clave', 'activo', 'orden',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden'  => 'integer',
    ];
}
