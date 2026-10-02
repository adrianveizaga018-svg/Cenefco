<?php

namespace App\Infrastructure\Imparticiones\Models;

use Illuminate\Database\Eloquent\Model;

class Imparte extends Model
{
    protected $table = 't_imparte';
    protected $primaryKey = 'id_imp';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_imp', 'id_us_reg', 'num_imp', 'periodo', 'gestion',
        'id_us', 'id_mat', 'paralelo', 'cupo', 'observacion_imp',
        'nro_resolucion_hcu', 'id_moodle', 'estado', 'fecha_reg',
    ];
}
