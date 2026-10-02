<?php

namespace App\Infrastructure\Expedidos\Models;

use Illuminate\Database\Eloquent\Model;

class Expedido extends Model
{
    protected $table      = 'web_expedido';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'nombre', 'orden', 'activo', 'created_at', 'updated_at',
    ];
}
