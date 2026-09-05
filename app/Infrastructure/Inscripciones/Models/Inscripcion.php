<?php

namespace App\Infrastructure\Inscripciones\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    protected $table = 't_inscripcion';
    protected $primaryKey = 'id_ins';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_us_reg', 'fecha_ins', 'id_us', 'id_imp',
        'id_plan', 'observacion_ins', 'observacion', 'periodo',
        'gestion', 'estado', 'fecha_reg',
        'id_vendedor', 'canal_venta',
        'email', 'telefono', 'documentos', 'campos_extra', 'origen',
    ];

    protected $casts = [
        'documentos'   => 'array',
        'campos_extra' => 'array',
    ];
}
