<?php

namespace App\Infrastructure\WhatsApp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WhatsappEtiqueta extends Model
{
    protected $table = 'whatsapp_etiquetas';

    protected $fillable = [
        'nombre',
        'color',
    ];

    public function conversaciones(): BelongsToMany
    {
        return $this->belongsToMany(
            WhatsappConversacion::class,
            'whatsapp_conversacion_etiqueta',
            'etiqueta_id',
            'conversacion_id'
        );
    }
}
