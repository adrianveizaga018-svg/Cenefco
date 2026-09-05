<?php

namespace App\Infrastructure\Monografias\Models;

use Illuminate\Database\Eloquent\Model;

class Monografia extends Model
{
    protected $table      = 't_monografia';
    protected $primaryKey = 'id_monografia';
    public    $timestamps = false;
    public    $incrementing = false;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_monografia', 'id_us_reg', 'num_monografia',
        'titulo_monografia', 'descripcion_monografia',
        'fecha_publicacion', 'autor', 'archivo',
        'estado', 'slug', 'fecha_reg',
    ];
}
