<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Aviso al estudiante de que Caja aprobó o rechazó el comprobante que subió desde su panel. */
class ComprobanteRevisadoNotification extends Notification
{
    public function __construct(
        public readonly string $nombre,
        public readonly string $programa,
        public readonly float $monto,
        public readonly bool $aprobado,
        public readonly ?string $motivo,
        public readonly float $saldoPendiente,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(($this->aprobado ? 'Pago confirmado' : 'Revisa tu comprobante').' — '.config('mail.from.name'))
            ->view('emails.comprobante-revisado', [
                'n'        => $this,
                'loginUrl' => config('app.frontend_url').'/auth-modern/login',
            ]);
    }
}
