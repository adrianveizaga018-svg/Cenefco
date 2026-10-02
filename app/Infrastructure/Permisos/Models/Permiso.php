<?php

namespace App\Infrastructure\Permisos\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    public $timestamps = false;

    protected $table = 'permisos';

    protected $fillable = [
        'codigo',
        'descripcion',
        'modulo',
    ];
}
