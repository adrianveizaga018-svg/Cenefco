<?php

namespace App\Infrastructure\CursosMigrados\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CursoMigrado extends Model
{
    protected $table = 'web_curso_migrado';

    protected $fillable = [
        'nombre',
        'slug',
        'url',
        'qr_path',
        'imagen_path',
        'periodo',
        'gestion',
        'fecha_inicio',
        'carga_horaria',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
    ];

    public function participantes(): HasMany
    {
        return $this->hasMany(ParticipanteMigrado::class, 'curso_id');
    }

    public function logos(): HasMany
    {
        return $this->hasMany(CursoMigradoLogo::class, 'curso_id')->orderBy('orden');
    }
}
