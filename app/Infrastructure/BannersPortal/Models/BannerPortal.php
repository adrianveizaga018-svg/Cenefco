<?php

namespace App\Infrastructure\BannersPortal\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BannerPortal extends Model
{
    protected $table = 'web_banner';

    protected $fillable = [
        'titulo',
        'slug',
        'subtitulo',
        'imagen_url',
        'imagen_alt',
        'imagen_movil_url',
        'enlace_url',
        'enlace_texto',
        'enlace_target',
        'enlace_url_2',
        'enlace_texto_2',
        'posicion',
        'orden',
        'activo',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin'    => 'datetime',
        'activo'       => 'boolean',
        'orden'        => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $base = Str::slug($model->titulo ?? '') ?: 'banner';
                $slug = $base;
                $i    = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $model->slug = $slug;
            }
        });
    }
}
