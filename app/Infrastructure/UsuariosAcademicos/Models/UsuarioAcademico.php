<?php

namespace App\Infrastructure\UsuariosAcademicos\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioAcademico extends Model
{
    protected $table = 't_usuario';
    protected $primaryKey = 'id_us';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_us', 'id_us_reg', 'tipoestudiante', 'nombre_usuario',
        'categoria', 'titulo_academico', 'titulo_academico2',
        'appaterno', 'apmaterno', 'nombre', 'ci', 'expedido',
        'telefono', 'celular', 'genero', 'email', 'direccion',
        'ciudad', 'estado_civil', 'pais', 'id_universidad',
        'id_carrera', 'estado', 'fecha_reg',
    ];
}
