<?php
namespace App\Infrastructure\PlanDoc\Models;
use Illuminate\Database\Eloquent\Model;
class PlanDoc extends Model {
    protected $table = 't_plandoc';
    protected $primaryKey = 'id_plandoc';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = ['id_plandoc', 'nombre', 'descripcion', 'estado'];
}
