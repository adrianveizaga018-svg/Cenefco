<?php

namespace App\Infrastructure\Testimonios\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Testimonio extends Model
{
    use SoftDeletes;

    protected $table = 'web_testimonio';

    protected $fillable = [
        'nombre',
        'slug',
        'cargo',
        'empresa',
        'testimonio',
        'calificacion',
        'foto_url',
        'foto_alt',
        'programa_id',
        'destacado',
        'orden',
        'estado',
    ];

    protected $casts = [
        'calificacion' => 'integer',
        'programa_id'  => 'integer',
        'destacado'    => 'boolean',
        'orden'        => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $base = Str::slug($model->nombre ?? '') ?: 'testimonio';
                $slug = $base;
                $i    = 1;
                while (static::withTrashed()->where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $model->slug = $slug;
            }
        });
    }
}
