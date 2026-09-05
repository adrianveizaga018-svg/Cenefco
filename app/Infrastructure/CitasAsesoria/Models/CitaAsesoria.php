<?php
namespace App\Infrastructure\CitasAsesoria\Models;
use Illuminate\Database\Eloquent\Model;
class CitaAsesoria extends Model {
    protected $table = 'web_cita_asesoria';
    protected $primaryKey = 'id_cita_asesoria';
    public $timestamps = false;
    protected $fillable = ['nombre', 'email', 'telefono', 'fecha', 'estado'];
}
