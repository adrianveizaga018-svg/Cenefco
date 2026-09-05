<?php

namespace App\Infrastructure\MediosPago\Models;

use Illuminate\Database\Eloquent\Model;

class MedioPago extends Model
{
    protected $table      = 'web_medio_pago';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $fillable = [
        'nombre', 'orden', 'activo',
        'created_at', 'updated_at',
    ];
}
