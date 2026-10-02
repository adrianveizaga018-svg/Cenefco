<?php

namespace App\Application\Universidades\Handlers;

use App\Application\Universidades\Commands\DeleteUniversidadCommand;
use App\Domain\Universidades\Contracts\UniversidadRepositoryInterface;

class DeleteUniversidadHandler
{
    public function __construct(private readonly UniversidadRepositoryInterface $repository) {}

    public function handle(DeleteUniversidadCommand $command): void
    {
        $this->repository->findById($command->id);
        $this->repository->delete($command->id);
    }
}
