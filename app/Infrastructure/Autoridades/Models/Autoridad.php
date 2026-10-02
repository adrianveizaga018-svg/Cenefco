<?php

namespace App\Infrastructure\Autoridades\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Autoridad extends Model
{
    protected $table = 'autoridades';

    public $timestamps = false;

    protected $fillable = [
        'secretaria_id',
        'nombre',
        'apellido',
        'cargo',
        'tipo',
        'perfil_profesional',
        'email_institucional',
        'foto_url',
        'orden',
        'activo',
        'publicado_web',
        'fecha_inicio_cargo',
        'fecha_fin_cargo',
        'slug',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'publicado_web' => 'boolean',
        'orden' => 'integer',
        'fecha_inicio_cargo' => 'date',
        'fecha_fin_cargo' => 'date',
        'created_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->nombre.' '.$model->apellido.' '.$model->cargo);
            }
        });
    }

    public function secretaria()
    {
        return $this->belongsTo(\App\Infrastructure\Secretarias\Models\Secretaria::class, 'secretaria_id');
    }
}
