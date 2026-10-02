<?php

namespace App\Infrastructure\Comunicados\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Comunicado extends Model
{
    protected $table = 'comunicados';

    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'slug',
        'resumen',
        'cuerpo',
        'imagen_url',
        'archivo_url',
        'estado',
        'destacado',
        'vistas',
        'fecha_publicacion',
        'created_at',
    ];

    protected $casts = [
        'destacado' => 'boolean',
        'vistas' => 'integer',
        'fecha_publicacion' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->titulo);
            }
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });

        static::updating(function (self $model) {
            if ($model->isDirty('titulo')) {
                $model->slug = Str::slug($model->titulo);
            }
        });
    }
}
