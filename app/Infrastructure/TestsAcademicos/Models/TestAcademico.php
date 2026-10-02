<?php
namespace App\Infrastructure\TestsAcademicos\Models;
use Illuminate\Database\Eloquent\Model;
class TestAcademico extends Model {
    protected $table = 't_test';
    protected $primaryKey = 'id_test';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id_test', 'nombre', 'habilitar_test', 'id_us_reg', 'fecha_reg', 'estado'];
}
