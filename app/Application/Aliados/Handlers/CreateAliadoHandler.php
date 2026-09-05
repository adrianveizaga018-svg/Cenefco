<?php

namespace App\Application\Aliados\Handlers;

use App\Application\Aliados\Commands\CreateAliadoCommand;
use App\Application\Aliados\DTOs\AliadoDTO;
use App\Domain\Aliados\Contracts\AliadoRepositoryInterface;

class CreateAliadoHandler
{
    public function __construct(private readonly AliadoRepositoryInterface $repository) {}

    public function handle(CreateAliadoCommand $command): AliadoDTO
    {
        return $this->repository->create([
            'nombre'      => $command->nombre,
            'logo_url'    => $command->logo_url,
            'logo_alt'    => $command->logo_alt,
            'url_sitio'   => $command->url_sitio,
            'descripcion' => $command->descripcion,
            'tipo'        => $command->tipo,
            'orden'       => $command->orden,
            'activo'      => $command->activo,
        ]);
    }
}
