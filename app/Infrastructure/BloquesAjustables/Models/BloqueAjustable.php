<?php
namespace App\Infrastructure\BloquesAjustables\Models;
use Illuminate\Database\Eloquent\Model;
class BloqueAjustable extends Model {
    protected $table = 't_bloqueajustable';
    protected $primaryKey = 'id_bloqueajustable';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id_bloqueajustable', 'id_pagina', 'id_bloqueplantilla', 'titulo', 'id_us_reg', 'fecha_reg', 'estado'];
}
