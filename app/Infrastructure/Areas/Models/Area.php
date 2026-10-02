<?php

namespace App\Infrastructure\Areas\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Area extends Model
{
    protected $table = 'web_area';

    protected $fillable = [
        'titulo', 'slug', 'descripcion',
        'logo_url', 'logo_alt', 'galeria',
        'color', 'icono', 'orden', 'activo',
        'meta_titulo', 'meta_descripcion',
    ];

    protected $casts = [
        'galeria' => 'array',
        'activo'  => 'boolean',
        'orden'   => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->titulo);
            }
        });
    }
}
