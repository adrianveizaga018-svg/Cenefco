<?php

namespace App\Application\TiposNorma\Handlers;

use App\Application\TiposNorma\Commands\UpdateTipoNormaCommand;
use App\Application\TiposNorma\DTOs\TipoNormaDTO;
use App\Domain\TiposNorma\Contracts\TipoNormaRepositoryInterface;

class UpdateTipoNormaHandler
{
    public function __construct(private readonly TipoNormaRepositoryInterface $repository) {}

    public function handle(UpdateTipoNormaCommand $command): TipoNormaDTO
    {
        return $this->repository->update($command->id, $command->data);
    }
}
