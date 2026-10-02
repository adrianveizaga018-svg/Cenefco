<?php

namespace App\Infrastructure\FechasDoc\Models;

use Illuminate\Database\Eloquent\Model;

class FechaDoc extends Model
{
    protected $table = 't_fechadoc';
    protected $primaryKey = 'id_fechadoc';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_fechadoc',
        'id_plandoc',
        'id_us_reg',
        'num_fechadoc',
        'nro_doc',
        'tipo_documento',
        'fecha_inicio',
        'fecha_fin',
        'obligatorio',
        'estado',
        'fecha_reg',
    ];
}
