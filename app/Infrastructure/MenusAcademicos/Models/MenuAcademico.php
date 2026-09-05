<?php
namespace App\Infrastructure\MenusAcademicos\Models;
use Illuminate\Database\Eloquent\Model;
class MenuAcademico extends Model {
    protected $table = 't_menu';
    protected $primaryKey = 'id_men';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id_men', 'id_mod', 'nombre', 'descripcion', 'id_us_reg', 'fecha_reg', 'estado'];
}
