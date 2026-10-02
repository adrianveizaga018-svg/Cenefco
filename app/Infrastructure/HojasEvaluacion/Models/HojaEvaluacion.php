<?php
namespace App\Infrastructure\HojasEvaluacion\Models;
use Illuminate\Database\Eloquent\Model;
class HojaEvaluacion extends Model {
    protected $table = 't_hojaevaluacion';
    protected $primaryKey = 'id_hojaevaluacion';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id_hojaevaluacion', 'id_us', 'id_us_tut', 'observacion', 'id_us_reg', 'fecha_reg', 'estado'];
}
