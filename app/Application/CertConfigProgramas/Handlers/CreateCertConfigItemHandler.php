<?php

namespace App\Application\CertConfigProgramas\Handlers;

use App\Application\CertConfigProgramas\Commands\CreateCertConfigItemCommand;
use App\Application\CertConfigProgramas\DTOs\CertConfigItemDTO;
use App\Domain\CertConfigProgramas\Contracts\CertConfigItemRepositoryInterface;

class CreateCertConfigItemHandler
{
    public function __construct(
        private readonly CertConfigItemRepositoryInterface $repo,
    ) {}

    public function handle(CreateCertConfigItemCommand $command): CertConfigItemDTO
    {
        if ($command->es_gratuito && $this->repo->existeGratuitoEnConfig($command->config_id)) {
            abort(422, 'Ya existe un certificado gratuito en esta configuración. Solo puede haber uno.');
        }

        $row = $this->repo->create([
            'config_id'   => $command->config_id,
            'plantilla_id'=> $command->plantilla_id,
            'nombre_cert' => $command->nombre_cert,
            'precio'      => $command->precio,
            'es_gratuito' => $command->es_gratuito,
            'orden'       => $command->orden,
            'activo'      => true,
        ]);

        return CertConfigItemDTO::fromRow($row);
    }
}
