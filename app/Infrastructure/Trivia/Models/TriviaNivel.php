<?php

namespace App\Infrastructure\Trivia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TriviaNivel extends Model
{
    protected $table = 'trivia_niveles';

    public $timestamps = true;

    protected $fillable = [
        'categoria_id',
        'nombre',
        'orden',
        'puntaje_base',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
        'puntaje_base' => 'integer',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(TriviaCategoria::class, 'categoria_id');
    }
}
