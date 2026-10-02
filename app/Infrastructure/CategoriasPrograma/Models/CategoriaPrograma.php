<?php

namespace App\Infrastructure\CategoriasPrograma\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CategoriaPrograma extends Model
{
    protected $table = 'web_categoria_programa';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'imagen_url',
        'imagen_alt',
        'icono',
        'color',
        'orden',
        'activo',
        'meta_titulo',
        'meta_descripcion',
        'tipo_programa_id',
        'comision_monto',
    ];

    protected $casts = [
        'orden'          => 'integer',
        'activo'         => 'boolean',
        'comision_monto' => 'decimal:2',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->nombre);
            }
        });

        static::updating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->nombre);
            }
        });
    }
}
