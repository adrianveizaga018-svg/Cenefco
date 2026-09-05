<?php

namespace App\Infrastructure\Articulos\Models;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    protected $table      = 't_articulo';
    protected $primaryKey = 'id_art';
    public    $timestamps = false;

    protected $fillable = [
        'titulo', 'slug', 'entradilla', 'contenido',
        'imagen_principal_url', 'imagen_alt', 'destacada',
        'fecha_publicacion', 'estado_web', 'meta_titulo', 'meta_descripcion',
        'estado', 'id_us_reg', 'num_art', 'vistas',
        'fecha_reg', 'updated_at', 'deleted_at',
    ];
}
