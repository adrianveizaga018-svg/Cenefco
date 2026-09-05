<?php

namespace App\Application\Ventas\Handlers;

use App\Application\Ventas\Commands\EnviarComprobanteCorreoCommand;
use App\Application\Ventas\QueryHandlers\GenerarComprobantePdfHandler;
use App\Domain\CorreosEnviados\Contracts\CorreoEnviadoRepositoryInterface;
use App\Domain\Ventas\Exceptions\EmailNoDisponibleException;
use App\Mail\ComprobanteInscripcion;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EnviarComprobanteCorreoHandler
{
    public function __construct(
        private readonly GenerarComprobantePdfHandler $pdfHandler,
        private readonly CorreoEnviadoRepositoryInterface $correoEnviadoRepository,
    ) {}

    public function handle(EnviarComprobanteCorreoCommand $command): string
    {
        ['venta' => $venta, 'pdf' => $pdf] = $this->pdfHandler->generar($command->idIns);

        $destinatario = $command->email ?: ($venta->estudiante_email ?? null);

        if (! $destinatario) {
            throw new EmailNoDisponibleException();
        }

        $asunto = "Comprobante de inscripción #{$venta->id_ins}";

        try {
            Mail::to($destinatario)->send(new ComprobanteInscripcion($venta, $pdf));
        } catch (Throwable $e) {
            $this->correoEnviadoRepository->create([
                'tipo'            => 'comprobante_inscripcion',
                'destinatario'    => $destinatario,
                'asunto'          => $asunto,
                'referencia_tipo' => 'inscripcion',
                'referencia_id'   => $command->idIns,
                'estado'          => 'fallido',
                'error'           => $e->getMessage(),
                'enviado_por'     => auth()->id(),
            ]);

            throw $e;
        }

        $this->correoEnviadoRepository->create([
            'tipo'            => 'comprobante_inscripcion',
            'destinatario'    => $destinatario,
            'asunto'          => $asunto,
            'referencia_tipo' => 'inscripcion',
            'referencia_id'   => $command->idIns,
            'estado'          => 'enviado',
            'enviado_por'     => auth()->id(),
        ]);

        return $destinatario;
    }
}
