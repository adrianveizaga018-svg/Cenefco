<?php

namespace App\Infrastructure\CursosMigrados\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CursoMigradoLogo extends Model
{
    protected $table = 'web_curso_migrado_logo';

    protected $fillable = ['curso_id', 'path', 'nombre', 'orden'];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(CursoMigrado::class, 'curso_id');
    }
}
