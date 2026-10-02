<?php

namespace App\Infrastructure\UsuariosTipoPrograma\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioTipoPrograma extends Model
{
    protected $table      = 't_usuariotipoprograma';
    protected $primaryKey = 'id_usuariotipoprograma';
    public $incrementing  = false;
    public $timestamps    = false;

    protected $fillable = [
        'id_usuariotipoprograma',
        'id_us',
        'id_us_reg',
        'num_usuariotipoprograma',
        'id_tipoprograma',
        'estado',
        'fecha_reg',
    ];
}
