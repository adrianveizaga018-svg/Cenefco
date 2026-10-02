<?php

namespace App\Infrastructure\UsuariosMoodle\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioMoodle extends Model
{
    protected $table      = 't_usuariomoodle';
    protected $primaryKey = 'id_usmoodle';
    public $incrementing  = false;
    public $timestamps    = false;

    protected $fillable = [
        'id_usmoodle',
        'id_us',
        'id_us_reg',
        'num_usmoodle',
        'id_moodle',
        'moodle_id_user',
        'estado',
        'fecha_reg',
    ];
}
