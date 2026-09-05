<?php

namespace App\Infrastructure\WhatsApp\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappMensaje extends Model
{
    protected $table = 'whatsapp_mensajes';

    protected $fillable = [
        'conversacion_id',
        'phone',
        'direccion',
        'tipo',
        'contenido',
        'whatsapp_message_id',
    ];

    public function conversacion()
    {
        return $this->belongsTo(WhatsappConversacion::class, 'conversacion_id');
    }
}
