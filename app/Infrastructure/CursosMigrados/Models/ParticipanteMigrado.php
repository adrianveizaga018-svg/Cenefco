<?php

namespace App\Infrastructure\CursosMigrados\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipanteMigrado extends Model
{
    protected $table = 'web_participante_migrado';

    protected $fillable = [
        'curso_id',
        'nombre_completo',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(CursoMigrado::class, 'curso_id');
    }
}
