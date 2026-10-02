<?php

namespace App\Infrastructure\Cursos\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $table      = 't_programa';
    protected $primaryKey = 'id_programa';

    public $timestamps = false;

    protected $fillable = [
        'id_programa',
        'id_us_reg',
        'nombre_programa',
        'slug',
        'descripcion',
        'objetivo',
        'dirigido',
        'requisitos',
        'inversion',
        'costo_monto',
        'creditaje',
        'nota',
        'foto',
        'titulo_documento1',
        'documento1',
        'imagen_banner_url',
        'imagen_alt',
        'url_video',
        'url_whatsapp',
        'url_whatsapp2',
        'imagenes',
        'inicio_actividades',
        'finalizacion_actividades',
        'inicio_inscripciones',
        'mes_facturacion',
        'id_tipoprograma',
        'categoria_web_id',
        'formulario_id',
        'area_id',
        'convenio_id',
        'vendedor_id',
        'id_plan',
        'id_plandoc',
        'id_imp',
        'estado',
        'estado_web',
        'destacado',
        'orden',
        'meta_titulo',
        'meta_descripcion',
        'mensaje_exito',
        'fecha_publicacion',
        'fecha_reg',
        'updated_at',
    ];

    protected $casts = [
        'id_programa'   => 'integer',
        'id_us_reg'     => 'integer',
        'destacado'     => 'boolean',
        'orden'         => 'integer',
        'estado'        => 'integer',
        'id_tipoprograma' => 'integer',
        'categoria_web_id' => 'integer',
        'area_id'       => 'integer',
        'id_plan'       => 'integer',
        'id_plandoc'    => 'integer',
        'id_imp'        => 'integer',
        'convenio_id'   => 'integer',
        'vendedor_id'   => 'integer',
        'costo_monto'   => 'decimal:2',
        'imagenes'      => 'array',
    ];
}
