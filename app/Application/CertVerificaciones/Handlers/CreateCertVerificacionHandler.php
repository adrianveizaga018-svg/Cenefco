<?php

namespace App\Application\CertVerificaciones\Handlers;

use App\Application\CertVerificaciones\Commands\CreateCertVerificacionCommand;
use App\Application\CertVerificaciones\DTOs\CertVerificacionDTO;
use App\Domain\CertVerificaciones\Contracts\CertVerificacionRepositoryInterface;

class CreateCertVerificacionHandler
{
    public function __construct(
        private readonly CertVerificacionRepositoryInterface $repository
    ) {}

    public function handle(CreateCertVerificacionCommand $command): CertVerificacionDTO
    {
        $row = $this->repository->create([
            'certificado_id'    => $command->certificado_id,
            'codigo_consultado' => $command->codigo_consultado,
            'resultado'         => $command->resultado,
            'ip_origen'         => $command->ip_origen,
            'user_agent'        => $command->user_agent,
            'pais'              => $command->pais,
            'created_at'        => now(),
        ]);

        return CertVerificacionDTO::fromRow($row);
    }
}
