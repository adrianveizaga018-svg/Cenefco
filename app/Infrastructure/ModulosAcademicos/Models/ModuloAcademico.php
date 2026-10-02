<?php
namespace App\Infrastructure\ModulosAcademicos\Models;
use Illuminate\Database\Eloquent\Model;
class ModuloAcademico extends Model {
    protected $table = 't_modulo';
    protected $primaryKey = 'id_mod';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id_mod', 'nombre', 'descripcion', 'posicion', 'id_us_reg', 'fecha_reg', 'estado'];
}
