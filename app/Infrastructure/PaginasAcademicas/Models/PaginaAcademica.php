<?php
namespace App\Infrastructure\PaginasAcademicas\Models;
use Illuminate\Database\Eloquent\Model;
class PaginaAcademica extends Model {
    protected $table = 't_pagina';
    protected $primaryKey = 'id_pagina';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id_pagina', 'nombre', 'descripcion', 'id_us_reg', 'fecha_reg', 'estado'];
}
