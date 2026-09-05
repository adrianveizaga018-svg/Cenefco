<?php

namespace App\Application\CertConfigProgramas\Handlers;

use App\Application\CertConfigProgramas\Commands\UpdateCertConfigItemCommand;
use App\Application\CertConfigProgramas\DTOs\CertConfigItemDTO;
use App\Domain\CertConfigProgramas\Contracts\CertConfigItemRepositoryInterface;
use App\Domain\CertConfigProgramas\Exceptions\CertConfigNotFoundException;

class UpdateCertConfigItemHandler
{
    public function __construct(
        private readonly CertConfigItemRepositoryInterface $repo,
    ) {}

    public function handle(UpdateCertConfigItemCommand $command): CertConfigItemDTO
    {
        $current = $this->repo->findById($command->id);
        if (! $current) {
            throw new CertConfigNotFoundException($command->id);
        }

        if ($command->es_gratuito && ! $current->es_gratuito) {
            if ($this->repo->existeGratuitoEnConfig($current->config_id, $command->id)) {
                abort(422, 'Ya existe un certificado gratuito en esta configuración. Solo puede haber uno.');
            }
        }

        $row = $this->repo->update($command->id, [
            'plantilla_id' => $command->plantilla_id,
            'nombre_cert'  => $command->nombre_cert,
            'precio'       => $command->precio,
            'es_gratuito'  => $command->es_gratuito,
            'orden'        => $command->orden,
            'activo'       => $command->activo,
        ]);

        return CertConfigItemDTO::fromRow($row);
    }
}
