<?php

namespace App\Infrastructure\Revistas\Models;

use Illuminate\Database\Eloquent\Model;

class Revista extends Model
{
    protected $table      = 't_revista';
    protected $primaryKey = 'id_revista';
    public    $timestamps = false;
    public    $incrementing = false;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_revista', 'id_us_reg', 'num_revista',
        'titulo_revista', 'descripcion_revista',
        'fecha_publicacion', 'archivo',
        'estado', 'slug', 'fecha_reg',
    ];
}
