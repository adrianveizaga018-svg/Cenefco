<?php

declare(strict_types=1);

namespace App\Infrastructure\Suscriptores\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Suscriptor extends Model
{
    use SoftDeletes;

    protected $table = 'web_suscriptor';

    protected $fillable = [
        'email',
        'nombre',
        'confirmado',
        'token_confirmacion',
        'activo',
        'origen',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'ip_origen',
        'fecha_confirmacion',
    ];

    protected $casts = [
        'confirmado'         => 'boolean',
        'activo'             => 'boolean',
        'fecha_confirmacion' => 'datetime',
    ];
}
