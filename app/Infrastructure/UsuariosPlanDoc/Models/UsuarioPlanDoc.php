<?php

namespace App\Infrastructure\UsuariosPlanDoc\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioPlanDoc extends Model
{
    protected $table      = 't_usuarioplandoc';
    protected $primaryKey = 'id_usuarioplandoc';
    public $incrementing  = false;
    public $timestamps    = false;

    protected $fillable = [
        'id_usuarioplandoc',
        'id_us',
        'id_us_reg',
        'num_usuarioplandoc',
        'id_plandoc',
        'estado',
        'fecha_reg',
    ];
}
