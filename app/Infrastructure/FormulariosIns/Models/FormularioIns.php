<?php
namespace App\Infrastructure\FormulariosIns\Models;
use Illuminate\Database\Eloquent\Model;
class FormularioIns extends Model {
    protected $table = 't_formularioins';
    protected $primaryKey = 'id_formins';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id_formins', 'id_imp', 'nombre', 'descripcion', 'id_us_reg', 'fecha_reg', 'estado'];
}
