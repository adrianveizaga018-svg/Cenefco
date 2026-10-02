<?php

namespace App\Application\TiposNorma\Handlers;

use App\Application\TiposNorma\Commands\CreateTipoNormaCommand;
use App\Application\TiposNorma\DTOs\TipoNormaDTO;
use App\Domain\TiposNorma\Contracts\TipoNormaRepositoryInterface;

class CreateTipoNormaHandler
{
    public function __construct(private readonly TipoNormaRepositoryInterface $repository) {}

    public function handle(CreateTipoNormaCommand $command): TipoNormaDTO
    {
        return $this->repository->create([
            'nombre' => $command->nombre,
            'sigla' => $command->sigla,
            'descripcion' => $command->descripcion,
            'activo' => $command->activo,
        ]);
    }
}
