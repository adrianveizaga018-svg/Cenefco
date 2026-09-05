<?php
namespace App\Infrastructure\Historiales\Models;
use Illuminate\Database\Eloquent\Model;
class Historial extends Model {
    protected $table = 't_historial';
    protected $primaryKey = 'id_historial';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id_historial', 'id_us', 'id_tiporeferencia', 'id_tipohistorial', 'descripcion', 'id_us_reg', 'fecha_reg', 'estado'];
}
