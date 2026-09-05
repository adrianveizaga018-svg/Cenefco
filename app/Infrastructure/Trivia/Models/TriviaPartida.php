<?php

namespace App\Infrastructure\Trivia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TriviaPartida extends Model
{
    protected $table = 'trivia_partidas';

    protected $fillable = [
        'modo',
        'categoria_id',
        'estado',
        'codigo_sala',
        'preguntas_ids',
    ];

    protected $casts = [
        'preguntas_ids' => 'array',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(TriviaCategoria::class, 'categoria_id');
    }

    public function jugadores(): HasMany
    {
        return $this->hasMany(TriviaPartidaJugador::class, 'partida_id');
    }
}
