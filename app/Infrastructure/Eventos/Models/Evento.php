<?php

namespace App\Infrastructure\Eventos\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Evento extends Model
{
    use SoftDeletes;

    protected $table = 'web_evento';

    protected $fillable = [
        'titulo', 'slug', 'entradilla', 'descripcion',
        'imagen_url', 'imagen_alt', 'tipo', 'modalidad',
        'lugar', 'url_transmision', 'url_registro',
        'gratuito', 'precio', 'cupo_maximo', 'programa_id',
        'fecha_inicio', 'fecha_fin', 'todo_el_dia',
        'destacado', 'meta_titulo', 'meta_descripcion', 'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin'    => 'datetime',
        'todo_el_dia'  => 'boolean',
        'gratuito'     => 'boolean',
        'destacado'    => 'boolean',
        'precio'       => 'decimal:2',
        'cupo_maximo'  => 'integer',
        'vistas'       => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($m) {
            if (empty($m->slug)) {
                $m->slug = Str::slug($m->titulo) . '-' . uniqid();
            }
        });
    }
}
