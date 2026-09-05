<?php

namespace App\Infrastructure\Trivia\Models;

use App\Infrastructure\Usuarios\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TriviaPartidaJugador extends Model
{
    protected $table = 'trivia_partida_jugadores';

    protected $fillable = [
        'partida_id',
        'usuario_id',
        'puntaje',
        'vidas',
        'estado',
        'orden_turno',
        'pregunta_actual_id',
        'pregunta_indice',
    ];

    protected $casts = [
        'puntaje' => 'integer',
        'vidas' => 'integer',
        'orden_turno' => 'integer',
        'pregunta_actual_id' => 'integer',
        'pregunta_indice' => 'integer',
    ];

    public function partida(): BelongsTo
    {
        return $this->belongsTo(TriviaPartida::class, 'partida_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function preguntaActual(): BelongsTo
    {
        return $this->belongsTo(TriviaPregunta::class, 'pregunta_actual_id');
    }
}
