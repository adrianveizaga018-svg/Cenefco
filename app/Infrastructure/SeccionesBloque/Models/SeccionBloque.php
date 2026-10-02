<?php
namespace App\Infrastructure\SeccionesBloque\Models;
use Illuminate\Database\Eloquent\Model;
class SeccionBloque extends Model {
    protected $table = 't_seccionbloque';
    protected $primaryKey = 'id_seccionbloque';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id_seccionbloque', 'id_bloqueajustable', 'titulo', 'contenido', 'id_us_reg', 'fecha_reg', 'estado'];
}
