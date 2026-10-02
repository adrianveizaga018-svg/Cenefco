<?php

namespace App\Infrastructure\UsuariosPlan\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioPlan extends Model
{
    protected $table      = 't_usuarioplan';
    protected $primaryKey = 'id_usuarioplan';
    public $incrementing  = false;
    public $timestamps    = false;

    protected $fillable = [
        'id_usuarioplan',
        'id_us',
        'id_us_reg',
        'num_usuarioplan',
        'id_plan',
        'estado',
        'fecha_reg',
    ];
}
