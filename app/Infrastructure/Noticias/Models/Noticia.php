<?php

namespace App\Infrastructure\Noticias\Models;

use App\Infrastructure\CategoriasNoticia\Models\CategoriaNoticia;
use App\Infrastructure\Etiquetas\Models\Etiqueta;
use App\Infrastructure\Usuarios\Models\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Noticia extends Model
{
    use SoftDeletes;

    protected $table = 'noticias';

    protected $fillable = [
        'categoria_id',
        'autor_id',
        'titulo',
        'slug',
        'entradilla',
        'cuerpo',
        'imagen_principal_url',
        'imagen_alt',
        'estado',
        'destacada',
        'fecha_publicacion',
        'vistas',
        'meta_titulo',
        'meta_descripcion',
    ];

    protected $casts = [
        'destacada' => 'boolean',
        'fecha_publicacion' => 'datetime',
        'vistas' => 'integer',
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

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaNoticia::class, 'categoria_id');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'autor_id');
    }

    public function etiquetas(): BelongsToMany
    {
        return $this->belongsToMany(Etiqueta::class, 'noticias_etiquetas', 'noticia_id', 'etiqueta_id');
    }
}
