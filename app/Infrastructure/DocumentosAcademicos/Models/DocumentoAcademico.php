<?php

namespace App\Infrastructure\DocumentosAcademicos\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoAcademico extends Model
{
    protected $table = 't_documento';
    protected $primaryKey = 'id_documento';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_documento',
        'id_us_reg',
        'num_documento',
        'id_us',
        'id_fechapago',
        'id_fechadoc',
        'fecha_dejo_fisico',
        'dejo_documento_fisico',
        'documento_digital',
        'observacion_doc',
        'estado',
        'fecha_reg',
    ];
}
