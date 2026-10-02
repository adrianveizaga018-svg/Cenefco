<?php

namespace App\Application\Universidades\Handlers;

use App\Application\Universidades\Commands\CreateUniversidadCommand;
use App\Application\Universidades\DTOs\UniversidadDTO;
use App\Domain\Universidades\Contracts\UniversidadRepositoryInterface;

class CreateUniversidadHandler
{
    public function __construct(private readonly UniversidadRepositoryInterface $repository) {}

    public function handle(CreateUniversidadCommand $command): UniversidadDTO
    {
        $row = $this->repository->create([
            'nombre_universidad' => $command->nombre_universidad,
            'id_ciudad'          => $command->id_ciudad,
            'id_tipouniversidad' => $command->id_tipouniversidad,
            'estado'             => $command->estado,
            'id_us_reg'          => $command->id_us_reg,
        ]);

        return UniversidadDTO::fromRow($row);
    }
}
