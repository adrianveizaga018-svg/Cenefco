<?php

namespace App\Application\Certificados\Handlers;

use App\Application\Certificados\Commands\UpdateCertificadoCommand;
use App\Application\Certificados\DTOs\CertificadoDTO;
use App\Domain\Certificados\Contracts\CertificadoRepositoryInterface;

class UpdateCertificadoHandler
{
    public function __construct(
        private readonly CertificadoRepositoryInterface $repository
    ) {}

    public function handle(UpdateCertificadoCommand $command): CertificadoDTO
    {
        $changes = array_filter([
            'nombre_en_certificado'   => $command->nombre_en_certificado,
            'programa_en_certificado' => $command->programa_en_certificado,
            'qr_url'                  => $command->qr_url,
            'archivo_url'             => $command->archivo_url,
            'archivo_miniatura_url'   => $command->archivo_miniatura_url,
            'estado'                  => $command->estado,
            'motivo_anulacion'        => $command->motivo_anulacion,
            'anulado_por'             => $command->anulado_por,
        ], fn ($v) => $v !== null);

        $changes['updated_at'] = now();

        $row = $this->repository->update($command->id, $changes);

        return CertificadoDTO::fromRow($row);
    }
}
