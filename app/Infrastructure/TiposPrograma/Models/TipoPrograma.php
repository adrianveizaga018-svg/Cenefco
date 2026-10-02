<?php

namespace App\Infrastructure\TiposPrograma\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPrograma extends Model
{
    protected $table = 't_tipoprograma';
    protected $primaryKey = 'id_tipoprograma';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_tipoprograma', 'nombre_tipoprograma', 'id_us_reg',
        'num_tipoprograma', 'estado', 'fecha_reg', 'per_modificar',
    ];
}
