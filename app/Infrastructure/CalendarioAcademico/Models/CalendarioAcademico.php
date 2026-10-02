<?php

declare(strict_types=1);

namespace App\Infrastructure\CalendarioAcademico\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarioAcademico extends Model
{
    protected $table = 'web_calendario_academico';

    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo',
        'color',
        'programa_id',
        'vendedor_id',
        'pagina',
        'duracion_dias',
        'costo_inflado',
        'descuento',
        'precio_vip',
        'observaciones',
        'fecha_inicio',
        'fecha_fin',
        'todo_el_dia',
        'destacado',
        'publico',
    ];

    protected $casts = [
        'programa_id'    => 'integer',
        'vendedor_id'    => 'integer',
        'duracion_dias'  => 'integer',
        'costo_inflado'  => 'decimal:2',
        'descuento'      => 'decimal:2',
        'precio_vip'     => 'decimal:2',
        'fecha_inicio'   => 'datetime',
        'fecha_fin'      => 'datetime',
        'todo_el_dia'    => 'boolean',
        'destacado'      => 'boolean',
        'publico'        => 'boolean',
    ];
}
