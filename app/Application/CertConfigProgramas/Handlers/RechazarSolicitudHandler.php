<?php

namespace App\Application\CertConfigProgramas\Handlers;

use App\Application\CertConfigProgramas\Commands\RechazarSolicitudCommand;
use App\Application\CertConfigProgramas\DTOs\CertSolicitudDTO;
use App\Domain\CertConfigProgramas\Contracts\CertSolicitudRepositoryInterface;
use App\Domain\CertConfigProgramas\Exceptions\CertSolicitudNotFoundException;

class RechazarSolicitudHandler
{
    public function __construct(
        private readonly CertSolicitudRepositoryInterface $repo,
    ) {}

    public function handle(RechazarSolicitudCommand $command): CertSolicitudDTO
    {
        $solicitud = $this->repo->findById($command->solicitud_id);
        if (! $solicitud) {
            throw new CertSolicitudNotFoundException($command->solicitud_id);
        }

        if ($solicitud->estado !== 'pendiente_revision') {
            abort(422, 'Solo se pueden rechazar solicitudes en estado pendiente_revision.');
        }

        $row = $this->repo->update($command->solicitud_id, [
            'estado'      => 'rechazado',
            'nota_admin'  => $command->nota_admin,
            'updated_at'  => now(),
        ]);

        return CertSolicitudDTO::fromRow($row);
    }
}
