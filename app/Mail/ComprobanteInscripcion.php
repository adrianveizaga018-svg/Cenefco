<?php

namespace App\Mail;

use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ComprobanteInscripcion extends Mailable
{
    use Queueable;

    public function __construct(
        public readonly object $venta,
        public readonly DomPdf $pdf,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Comprobante de inscripción #{$this->venta->id_ins} — " . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.comprobante_inscripcion',
            with: ['venta' => $this->venta],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => $this->pdf->output(),
                "comprobante-{$this->venta->id_ins}.pdf"
            )->withMime('application/pdf'),
        ];
    }
}
