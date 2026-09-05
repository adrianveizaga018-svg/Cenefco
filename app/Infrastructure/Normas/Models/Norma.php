<?php

namespace App\Infrastructure\Normas\Models;

use App\Infrastructure\TiposNorma\Models\TipoNorma;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Norma extends Model
{
    protected $table = 'normas';

    protected $fillable = [
        'tipo_norma_id',
        'publicado_por',
        'numero',
        'titulo',
        'resumen',
        'texto_completo',
        'archivo_pdf_url',
        'fecha_promulgacion',
        'fecha_publicacion_gaceta',
        'estado_vigencia',
        'activo',
        'tema_principal',
        'palabras_clave',
        'vistas',
        'slug',
    ];

    protected $casts = [
        'tipo_norma_id' => 'integer',
        'publicado_por' => 'integer',
        'fecha_promulgacion' => 'date',
        'fecha_publicacion_gaceta' => 'date',
        'vistas' => 'integer',
        'activo' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->numero.'-'.$model->titulo);
            }
        });
    }

    public function tipo()
    {
        return $this->belongsTo(TipoNorma::class, 'tipo_norma_id');
    }
}
