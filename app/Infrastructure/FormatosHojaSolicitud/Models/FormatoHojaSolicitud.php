<?php
namespace App\Infrastructure\FormatosHojaSolicitud\Models;
use Illuminate\Database\Eloquent\Model;
class FormatoHojaSolicitud extends Model {
    protected $table = 't_formato_hoja_solicitud';
    protected $primaryKey = 'id_formato_hoja_solicitud';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id_formato_hoja_solicitud', 'nombre', 'descripcion', 'id_us_reg', 'fecha_reg', 'estado'];
}
