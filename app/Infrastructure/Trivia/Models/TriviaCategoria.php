<?php

namespace App\Infrastructure\Trivia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TriviaCategoria extends Model
{
    use SoftDeletes;

    protected $table = 'trivia_categorias';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'imagen_url',
        'color',
        'curso_id',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
        'curso_id' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->nombre);
            }
        });
    }

    public function niveles(): HasMany
    {
        return $this->hasMany(TriviaNivel::class, 'categoria_id');
    }
}
