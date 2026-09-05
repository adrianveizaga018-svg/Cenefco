<?php

namespace App\Infrastructure\Empleados\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empleado extends Model
{
    use SoftDeletes;

    protected $table = 'empleado';

    protected $fillable = [
        'nombre_completo',
        'cargo',
        'sueldo_mensual',
        'ci',
        'carnet_pdf',
        'correo',
        'celular_personal',
        'celular_corporativo',
        'direccion',
        'fecha_ingreso',
        'activo',
    ];

    protected $casts = [
        'sueldo_mensual' => 'decimal:2',
        'fecha_ingreso'  => 'date',
        'activo'         => 'boolean',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
