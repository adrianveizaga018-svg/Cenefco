<?php

namespace App\Infrastructure\Boletines\Models;

use Illuminate\Database\Eloquent\Model;

class Boletin extends Model
{
    protected $table      = 't_boletin';
    protected $primaryKey = 'id_boletin';
    public    $timestamps = false;
    public    $incrementing = false;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_boletin', 'id_us_reg', 'num_boletin',
        'titulo_pagina', 'titulo_boletin', 'descripcion_boletin',
        'estado', 'imagen_url', 'slug', 'fecha_reg',
    ];
}
