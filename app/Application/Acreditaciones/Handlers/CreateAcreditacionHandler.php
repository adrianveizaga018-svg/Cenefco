<?php

namespace App\Application\Acreditaciones\Handlers;

use App\Application\Acreditaciones\Commands\CreateAcreditacionCommand;
use App\Application\Acreditaciones\DTOs\AcreditacionDTO;
use App\Domain\Acreditaciones\Contracts\AcreditacionRepositoryInterface;

class CreateAcreditacionHandler
{
    public function __construct(
        private readonly AcreditacionRepositoryInterface $repository,
    ) {}

    public function handle(CreateAcreditacionCommand $command): AcreditacionDTO
    {
        return $this->repository->create([
            'nombre'            => $command->nombre,
            'entidad_otorgante' => $command->entidad_otorgante,
            'tipo'              => $command->tipo,
            'descripcion'       => $command->descripcion,
            'logo_url'          => $command->logo_url,
            'logo_alt'          => $command->logo_alt,
            'url_verificacion'  => $command->url_verificacion,
            'fecha_obtencion'   => $command->fecha_obtencion,
            'fecha_vencimiento' => $command->fecha_vencimiento,
            'orden'             => $command->orden,
            'activo'            => $command->activo,
        ]);
    }
}
