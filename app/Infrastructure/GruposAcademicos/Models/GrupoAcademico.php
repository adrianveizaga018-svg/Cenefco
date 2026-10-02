<?php
namespace App\Infrastructure\GruposAcademicos\Models;
use Illuminate\Database\Eloquent\Model;
class GrupoAcademico extends Model {
    protected $table = 't_grupo';
    protected $primaryKey = 'id_grupo';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id_grupo', 'id_test', 'nombre', 'id_us_reg', 'fecha_reg', 'estado'];
}
