<?php

namespace App\Infrastructure\Etiquetas\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Etiqueta extends Model
{
    protected $table = 'web_etiqueta';

    public $timestamps = false;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'nombre',
        'slug',
        'color',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->nombre);
            }
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });
    }
}
