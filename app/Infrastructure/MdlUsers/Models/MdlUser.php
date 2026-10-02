<?php

namespace App\Infrastructure\MdlUsers\Models;

use Illuminate\Database\Eloquent\Model;

class MdlUser extends Model
{
    protected $table = 'mdl_user';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id', 'id_us_reg', 'num_modusuario', 'nombre_usuario', 'nombre',
        'appaterno', 'apmaterno', 'ci', 'expedido', 'telefono', 'celular',
        'email', 'direccion', 'ciudad', 'per_modificar', 'fecha_reg', 'estado',
    ];

    protected $hidden = ['password'];
}
