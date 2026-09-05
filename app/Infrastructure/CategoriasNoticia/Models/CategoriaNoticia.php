<?php

namespace App\Infrastructure\CategoriasNoticia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CategoriaNoticia extends Model
{
    protected $table = 'categorias_noticia';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'color_hex',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
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
}
