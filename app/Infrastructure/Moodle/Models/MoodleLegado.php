<?php

namespace App\Infrastructure\Moodle\Models;

use Illuminate\Database\Eloquent\Model;

class MoodleLegado extends Model
{
    protected $table = 't_moodle';
    protected $primaryKey = 'id_moodle';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_moodle', 'id_us_reg', 'num_moodle', 'titulo_moodle',
        'cp_moodle_servidor', 'cp_moodle_base_datos', 'cp_moodle_usuario_bd',
        'cp_moodle_contrasena', 'cp_url_campus', 'fecha_reg', 'estado',
    ];
}
