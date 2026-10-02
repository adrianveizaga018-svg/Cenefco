<?php

namespace App\Infrastructure\ConfiguracionesAcademicas\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionAcademica extends Model
{
    protected $table = 't_configuracion';
    protected $primaryKey = 'id_conf';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_conf', 'gestion', 'id_plan', 'descripcion', 'id_us_reg', 'fecha_reg', 'estado',
    ];
}
