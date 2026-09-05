<?php

namespace App\Infrastructure\Asesores\Models;

use Illuminate\Database\Eloquent\Model;

class Asesor extends Model
{
    protected $table = 'web_asesor';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nombre', 'telefono', 'email', 'especialidad', 'disponible', 'activo',
    ];

    protected $casts = [
        'disponible' => 'boolean',
        'activo'     => 'boolean',
    ];
}
