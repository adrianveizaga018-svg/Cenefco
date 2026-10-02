<?php

namespace App\Infrastructure\Vendedores\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendedor extends Model
{
    use SoftDeletes;

    protected $table = 'vendedores';

    protected $fillable = [
        'nombre',
        'apellido',
        'ci',
        'telefono',
        'email',
        'foto',
        'pagina',
        'meta_ventas',
        'comision',
        'activo',
        'usuario_id',
    ];

    protected $casts = [
        'activo'      => 'boolean',
        'meta_ventas' => 'float',
        'comision'    => 'float',
        'usuario_id'  => 'integer',
    ];
}
