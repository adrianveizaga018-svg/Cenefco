<?php

namespace App\Infrastructure\DocentesPerfil\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DocentePerfil extends Model
{
    use SoftDeletes;

    protected $table = 'web_docente_perfil';

    protected $fillable = [
        'usuario_id',
        'nombre_completo',
        'slug',
        'titulo_academico',
        'especialidad',
        'biografia',
        'foto_url',
        'foto_alt',
        'email_publico',
        'telefono',
        'linkedin_url',
        'twitter_url',
        'sitio_web_url',
        'tipo',
        'mostrar_en_web',
        'orden',
        'estado',
    ];

    protected $casts = [
        'usuario_id'    => 'integer',
        'mostrar_en_web' => 'boolean',
        'orden'         => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $base = Str::slug($model->nombre_completo) ?: 'docente';
                $slug = $base;
                $i = 1;
                while (static::withTrashed()->where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $model->slug = $slug;
            }
        });
    }
}
