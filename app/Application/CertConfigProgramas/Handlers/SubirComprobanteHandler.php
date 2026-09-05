<?php

namespace App\Application\CertConfigProgramas\Handlers;

use App\Application\CertConfigProgramas\Commands\SubirComprobanteCommand;
use App\Application\CertConfigProgramas\DTOs\CertSolicitudDTO;
use App\Domain\CertConfigProgramas\Contracts\CertSolicitudRepositoryInterface;
use App\Domain\CertConfigProgramas\Exceptions\CertSolicitudNotFoundException;

class SubirComprobanteHandler
{
    public function __construct(
        private readonly CertSolicitudRepositoryInterface $repo,
    ) {}

    public function handle(SubirComprobanteCommand $command): CertSolicitudDTO
    {
        $solicitud = $this->repo->findById($command->solicitud_id);
        if (! $solicitud) {
            throw new CertSolicitudNotFoundException($command->solicitud_id);
        }

        if (! in_array($solicitud->estado, ['pendiente_pago', 'rechazado'])) {
            abort(422, 'Solo se puede subir comprobante en estado pendiente_pago o rechazado.');
        }

        $row = $this->repo->update($command->solicitud_id, [
            'comprobante_url' => $command->comprobante_url,
            'estado'          => 'pendiente_revision',
            'nota_admin'      => null,
            'updated_at'      => now(),
        ]);

        return CertSolicitudDTO::fromRow($row);
    }
}
