<?php
namespace App\Infrastructure\ArchivosAcademicos\Models;
use Illuminate\Database\Eloquent\Model;
class ArchivoAcademico extends Model {
    protected $table = 't_archivo';
    protected $primaryKey = 'id_arch';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id_arch', 'nombre', 'ruta', 'tipo', 'id_us_reg', 'fecha_reg', 'estado'];
}
