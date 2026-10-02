<?php

namespace App\Infrastructure\CertPlantillaCampos\Models;

use Illuminate\Database\Eloquent\Model;

class CertPlantillaCampo extends Model
{
    protected $table = 't_cert_plantilla_campo';

    protected $fillable = [
        'plantilla_id',
        'clave',
        'etiqueta',
        'tipo',
        'pos_x_pct',
        'pos_y_pct',
        'ancho_pct',
        'alto_pct',
        'fuente',
        'tamano_pt',
        'color',
        'alineacion',
        'negrita',
        'cursiva',
        'mayusculas',
        'valor_fijo',
        'activo',
        'orden',
    ];

    public $timestamps = false;
}
