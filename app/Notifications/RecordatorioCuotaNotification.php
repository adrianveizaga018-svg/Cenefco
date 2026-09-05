<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecordatorioCuotaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly object $venta,
        private readonly string $nroCuota,
        private readonly float  $monto,
        private readonly string $fechaVence,
        private readonly string $nombreEstudiante,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $programa  = $this->venta->nombre_programa ?? 'tu programa';
        $idIns     = $this->venta->id_ins ?? '—';
        $monto     = number_format($this->monto, 2);

        return (new MailMessage)
            ->subject("Recordatorio de pago — Cuota {$this->nroCuota} | CENEFCO")
            ->greeting("Estimado/a {$this->nombreEstudiante},")
            ->line("Te recordamos que tienes una cuota por vencer en los próximos días.")
            ->line("**Programa:** {$programa}")
            ->line("**Cuota N°:** {$this->nroCuota}")
            ->line("**Monto:** Bs. {$monto}")
            ->line("**Fecha de vencimiento:** {$this->fechaVence}")
            ->line('Por favor realiza tu pago a tiempo para evitar recargos por mora.')
            ->action('Ver mi comprobante', rtrim(config('app.url'), '/') . "/api/v1/ventas/{$idIns}/pdf")
            ->line('Si ya realizaste el pago, por favor ignora este mensaje.')
            ->salutation('Equipo CENEFCO — Centro Nacional de Educación y Formación Continua');
    }
}
