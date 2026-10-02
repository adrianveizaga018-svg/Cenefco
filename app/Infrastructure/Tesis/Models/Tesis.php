<?php

namespace App\Infrastructure\Tesis\Models;

use Illuminate\Database\Eloquent\Model;

class Tesis extends Model
{
    protected $table      = 't_tesis';
    protected $primaryKey = 'id_tesis';
    public    $timestamps = false;
    public    $incrementing = false;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_tesis', 'id_us_reg', 'num_tesis',
        'titulo_tesis', 'descripcion_tesis',
        'fecha_publicacion', 'autor', 'tipo_tesis',
        'archivo', 'estado', 'slug', 'fecha_reg',
    ];
}
