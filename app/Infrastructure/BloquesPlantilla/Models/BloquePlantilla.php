<?php
namespace App\Infrastructure\BloquesPlantilla\Models;
use Illuminate\Database\Eloquent\Model;
class BloquePlantilla extends Model {
    protected $table = 't_bloqueplantilla';
    protected $primaryKey = 'id_bloqueplantilla';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id_bloqueplantilla', 'nombre', 'descripcion', 'id_us_reg', 'fecha_reg', 'estado'];
}
