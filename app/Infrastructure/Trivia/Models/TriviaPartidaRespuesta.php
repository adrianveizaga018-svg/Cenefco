<?php

namespace App\Infrastructure\Trivia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TriviaPartidaRespuesta extends Model
{
    protected $table = 'trivia_partida_respuestas';

    public $timestamps = false;

    protected $fillable = [
        'partida_id',
        'jugador_id',
        'pregunta_id',
        'opcion_id',
        'es_correcta',
        'tiempo_respuesta_ms',
    ];

    protected $casts = [
        'es_correcta' => 'boolean',
        'tiempo_respuesta_ms' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at ??= now();
        });
    }

    public function partida(): BelongsTo
    {
        return $this->belongsTo(TriviaPartida::class, 'partida_id');
    }

    public function jugador(): BelongsTo
    {
        return $this->belongsTo(TriviaPartidaJugador::class, 'jugador_id');
    }

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(TriviaPregunta::class, 'pregunta_id');
    }

    public function opcion(): BelongsTo
    {
        return $this->belongsTo(TriviaOpcion::class, 'opcion_id');
    }
}
