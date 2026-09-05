<?php

namespace App\Infrastructure\Notificaciones\Models;

use Illuminate\Database\Eloquent\Model;

class NotificacionPreferencia extends Model
{
    protected $table = 'notificacion_preferencias';

    protected $fillable = ['usuario_id', 'tipo', 'activa'];

    protected $casts = ['activa' => 'boolean'];
}
