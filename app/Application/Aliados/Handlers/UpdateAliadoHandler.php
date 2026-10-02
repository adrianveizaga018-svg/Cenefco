<?php

namespace App\Application\Aliados\Handlers;

use App\Application\Aliados\Commands\UpdateAliadoCommand;
use App\Application\Aliados\DTOs\AliadoDTO;
use App\Domain\Aliados\Contracts\AliadoRepositoryInterface;

class UpdateAliadoHandler
{
    public function __construct(private readonly AliadoRepositoryInterface $repository) {}

    public function handle(UpdateAliadoCommand $command): AliadoDTO
    {
        $data = array_filter([
            'nombre'      => $command->nombre,
            'logo_url'    => $command->logo_url,
            'logo_alt'    => $command->logo_alt,
            'url_sitio'   => $command->url_sitio,
            'descripcion' => $command->descripcion,
            'tipo'        => $command->tipo,
            'orden'       => $command->orden,
            'activo'      => $command->activo,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
