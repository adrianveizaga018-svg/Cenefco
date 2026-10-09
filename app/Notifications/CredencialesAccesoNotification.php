<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Correo con las credenciales de acceso de un estudiante inscrito en Caja.
 * No lleva contraseña: lleva un enlace para que el estudiante cree la suya.
 */
class CredencialesAccesoNotification extends Notification
{
    /** @param string[] $programas nombres de los programas en que está inscrito */
    public function __construct(
        public readonly string $token,
        public readonly array $programas = [],
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = config('app.frontend_url').'/auth-modern/create-password?'.http_build_query([
            'token' => $this->token,
            'email' => $notifiable->email,
            'tipo'  => 'activacion',
        ]);

        return (new MailMessage)
            ->subject('Tus credenciales de acceso — '.config('mail.from.name'))
            ->view('emails.credenciales-acceso', [
                'url'        => $url,
                'user'       => $notifiable,
                'programas'  => $this->programas,
                'loginUrl'   => config('app.frontend_url').'/auth-modern/login',
                'diasVigencia' => intdiv((int) config('auth.passwords.activacion.expire'), 60 * 24),
            ]);
    }
}
