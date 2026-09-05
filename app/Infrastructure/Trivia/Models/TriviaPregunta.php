<?php

namespace App\Infrastructure\Trivia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TriviaPregunta extends Model
{
    use SoftDeletes;

    protected $table = 'trivia_preguntas';

    protected $fillable = [
        'categoria_id',
        'nivel_id',
        'enunciado',
        'imagen_url',
        'tiempo_limite_segundos',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'tiempo_limite_segundos' => 'integer',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(TriviaCategoria::class, 'categoria_id');
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(TriviaNivel::class, 'nivel_id');
    }

    public function opciones(): HasMany
    {
        return $this->hasMany(TriviaOpcion::class, 'pregunta_id')->orderBy('orden');
    }
}
