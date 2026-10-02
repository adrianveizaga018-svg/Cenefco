<?php

namespace App\Infrastructure\Profesiones\Models;

use Illuminate\Database\Eloquent\Model;

class Profesion extends Model
{
    protected $table      = 'web_profesion';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'nombre', 'orden', 'activo',
        'created_at', 'updated_at',
    ];
}
