<?php
namespace App\Infrastructure\FormulariosAcademicos\Models;
use Illuminate\Database\Eloquent\Model;
class FormularioAcademico extends Model {
    protected $table = 't_formulario';
    protected $primaryKey = 'id_formulario';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id_formulario', 'nombre', 'descripcion', 'id_us_reg', 'fecha_reg', 'estado'];
}
