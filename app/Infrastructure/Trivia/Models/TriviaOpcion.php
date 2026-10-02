<?php

namespace App\Infrastructure\Trivia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TriviaOpcion extends Model
{
    protected $table = 'trivia_opciones';

    protected $fillable = [
        'pregunta_id',
        'texto',
        'es_correcta',
        'orden',
    ];

    protected $casts = [
        'es_correcta' => 'boolean',
        'orden' => 'integer',
    ];

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(TriviaPregunta::class, 'pregunta_id');
    }
}
