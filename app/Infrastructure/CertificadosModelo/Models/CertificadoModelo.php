<?php
namespace App\Infrastructure\CertificadosModelo\Models;
use Illuminate\Database\Eloquent\Model;
class CertificadoModelo extends Model {
    protected $table = 't_certificadomodelo';
    protected $primaryKey = 'id_certmod';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id_certmod', 'nombre', 'descripcion', 'id_us_reg', 'fecha_reg', 'estado'];
}
