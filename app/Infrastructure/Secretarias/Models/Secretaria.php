<?php

namespace App\Infrastructure\Secretarias\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Secretaria extends Model
{
    protected $table = 'secretarias';

    protected $fillable = [
        'nombre',
        'sigla',
        'atribuciones',
        'direccion_fisica',
        'telefono',
        'email',
        'horario_atencion',
        'foto_titular_url',
        'orden_organigrama',
        'activa',
        'slug',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'orden_organigrama' => 'integer',
    ];

    public function autoridades()
    {
        return $this->hasMany(\App\Infrastructure\Autoridades\Models\Autoridad::class, 'secretaria_id');
    }

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
