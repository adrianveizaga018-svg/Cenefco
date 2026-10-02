<?php

namespace App\Infrastructure\Universidades\Models;

use Illuminate\Database\Eloquent\Model;

class Universidad extends Model
{
    protected $table      = 't_universidad';
    protected $primaryKey = 'id_universidad';
    public    $timestamps = false;

    protected $fillable = [
        'nombre_universidad', 'id_ciudad', 'id_tipouniversidad',
        'estado', 'id_us_reg',
    ];
}
