<?php

namespace App\Infrastructure\DocumentosTransparencia\Models;

use App\Infrastructure\Secretarias\Models\Secretaria;
use App\Infrastructure\TiposDocumentoTransparencia\Models\TipoDocumentoTransparencia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Documento extends Model
{
    protected $table = 'documentos_transparencia';

    public $timestamps = false;

    protected $fillable = [
        'tipo_documento_id',
        'secretaria_id',
        'publicado_por',
        'titulo',
        'descripcion',
        'archivo_url',
        'gestion',
        'fecha_publicacion',
        'activo',
        'slug',
        'created_at',
    ];

    protected $casts = [
        'gestion' => 'integer',
        'fecha_publicacion' => 'date',
        'activo' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function tipoDocumento(): BelongsTo
    {
        return $this->belongsTo(TipoDocumentoTransparencia::class, 'tipo_documento_id');
    }

    public function secretaria(): BelongsTo
    {
        return $this->belongsTo(Secretaria::class, 'secretaria_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->titulo).'-'.uniqid();
            }
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });
    }
}
