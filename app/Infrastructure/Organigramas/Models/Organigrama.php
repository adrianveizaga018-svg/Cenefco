<?php

namespace App\Infrastructure\Organigramas\Models;

use Illuminate\Database\Eloquent\Model;

class Organigrama extends Model
{
    protected $table = 'organigrama';

    public $timestamps = false;

    protected $fillable = [
        'secretaria_id',
        'parent_id',
        'nivel',
        'orden',
        'imagen_url',
        'fecha_actualizacion',
        'vigente',
    ];

    protected $casts = [
        'vigente' => 'boolean',
        'nivel' => 'integer',
        'orden' => 'integer',
        'fecha_actualizacion' => 'date',
    ];

    public function secretaria()
    {
        return $this->belongsTo(\App\Infrastructure\Secretarias\Models\Secretaria::class, 'secretaria_id');
    }

    public function parent()
    {
        return $this->belongsTo(Organigrama::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Organigrama::class, 'parent_id');
    }
}
