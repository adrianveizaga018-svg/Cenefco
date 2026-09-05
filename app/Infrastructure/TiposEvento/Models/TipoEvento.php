<?php

namespace App\Infrastructure\TiposEvento\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TipoEvento extends Model
{
    protected $table = 'tipos_evento';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'slug',
        'color_hex',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
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
