<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Aviso al estudiante de que su inscripción se cambió a otro programa. */
class CambioProgramaNotification extends Notification
{
    /** @param array<int, array{descripcion: string, monto_pendiente: float, fecha_vencimiento: ?string}> $cuotas */
    public function __construct(
        public readonly string $nombre,
        public readonly string $programaAnterior,
        public readonly string $programaNuevo,
        public readonly float $reconocido,
        public readonly float $saldoPendiente,
        public readonly float $saldoAFavor,
        public readonly array $cuotas,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu cambio de programa — '.config('mail.from.name'))
            ->view('emails.cambio-programa', [
                'n'        => $this,
                'loginUrl' => config('app.frontend_url').'/auth-modern/login',
            ]);
    }
}
