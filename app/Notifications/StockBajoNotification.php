<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class StockBajoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Collection $productos,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mensaje = (new MailMessage)
            ->subject('⚠️ Alerta de stock bajo — '.config('app.name'))
            ->greeting('Hola '.$notifiable->nombre)
            ->line("{$this->productos->count()} producto(s) tienen stock bajo o agotado:");

        foreach ($this->productos->take(10) as $producto) {
            $mensaje->line("• **{$producto->nombre}**: {$producto->stock_actual} unidades (mínimo: {$producto->stock_minimo})");
        }

        return $mensaje
            ->action('Ver inventario', config('app.frontend_url').'/inventario')
            ->line('Revisa y repone el stock para evitar problemas de venta.');
    }
}
