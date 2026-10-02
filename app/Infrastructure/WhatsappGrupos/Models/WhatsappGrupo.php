<?php
namespace App\Infrastructure\WhatsappGrupos\Models;
use Illuminate\Database\Eloquent\Model;
class WhatsappGrupo extends Model {
    protected $table = 'web_whatsapp_grupo';
    protected $fillable = [
        'imparte_id','nombre','enlace_invitacion','capacidad_maxima',
        'miembros_actuales','descripcion','activo','orden','fecha_expiracion_enlace',
    ];
    protected $casts = [
        'imparte_id' => 'integer',
        'capacidad_maxima' => 'integer',
        'miembros_actuales' => 'integer',
        'activo' => 'boolean',
        'orden' => 'integer',
        'fecha_expiracion_enlace' => 'datetime',
    ];
}
