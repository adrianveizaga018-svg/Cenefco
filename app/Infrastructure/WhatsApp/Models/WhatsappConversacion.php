<?php

namespace App\Infrastructure\WhatsApp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WhatsappConversacion extends Model
{
    protected $table = 'whatsapp_conversaciones';

    protected $fillable = [
        'phone',
        'nombre',
        'estado',
        'contexto',
        'cliente_id',
        'asesor_id',
    ];

    protected $casts = [
        'contexto' => 'array',
    ];

    public function etiquetas(): BelongsToMany
    {
        return $this->belongsToMany(
            WhatsappEtiqueta::class,
            'whatsapp_conversacion_etiqueta',
            'conversacion_id',
            'etiqueta_id'
        );
    }
}
