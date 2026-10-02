<?php

namespace App\Application\Certificados\Handlers;

use App\Application\Certificados\Commands\GenerarCertificadosLoteCommand;
use App\Application\Certificados\Services\CertificadoService;

class GenerarCertificadosLoteHandler
{
    public function __construct(
        private readonly CertificadoService $certificadoService,
    ) {}

    public function handle(GenerarCertificadosLoteCommand $command): array
    {
        return $this->certificadoService->generarLote(
            $command->imparteId,
            $command->plantillaId,
            $command->usuarioIds,
        );
    }
}
