<?php

namespace App\Application\TiposNorma\Handlers;

use App\Application\TiposNorma\Commands\DeleteTipoNormaCommand;
use App\Domain\TiposNorma\Contracts\TipoNormaRepositoryInterface;

class DeleteTipoNormaHandler
{
    public function __construct(private readonly TipoNormaRepositoryInterface $repository) {}

    public function handle(DeleteTipoNormaCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
