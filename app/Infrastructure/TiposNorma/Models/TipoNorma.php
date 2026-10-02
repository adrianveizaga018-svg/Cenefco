<?php

namespace App\Infrastructure\TiposNorma\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TipoNorma extends Model
{
    protected $table = 'tipos_norma';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'sigla',
        'descripcion',
        'activo',
        'slug',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->nombre);
            }
        });
    }
}
