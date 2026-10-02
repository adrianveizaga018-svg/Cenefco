<?php

namespace App\Infrastructure\Pagos\Models;

use Illuminate\Database\Eloquent\Model;

class PagoOnlineSession extends Model
{
    protected $table = 'web_pago_online_session';

    protected $fillable = [
        'id_ins', 'id_fechapago', 'proveedor',
        'checkout_id', 'reference', 'checkout_url', 'order_id',
        'sale_id', 'transaction_id',
        'monto', 'moneda', 'estado', 'expires_at', 'webhook_payload',
    ];

    protected $casts = [
        'monto'           => 'float',
        'webhook_payload' => 'array',
        'expires_at'      => 'datetime',
    ];
}
