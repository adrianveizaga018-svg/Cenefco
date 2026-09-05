<?php

namespace App\Infrastructure\UsuariosPrograma\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioPrograma extends Model
{
    protected $table      = 't_usuarioprograma';
    protected $primaryKey = 'id_usuarioprograma';
    public $incrementing  = false;
    public $timestamps    = false;

    protected $fillable = [
        'id_usuarioprograma',
        'id_us',
        'id_us_reg',
        'num_usuarioprograma',
        'id_programa',
        'id_tipoprograma',
        'estado',
        'fecha_reg',
    ];
}
