<?php

namespace App\Application\SeccionesBloque\Handlers;

use App\Application\SeccionesBloque\Commands\DeleteSeccionBloqueCommand;
use App\Domain\SeccionesBloque\Contracts\SeccionBloqueRepositoryInterface;

class DeleteSeccionBloqueHandler
{
    public function __construct(
        private readonly SeccionBloqueRepositoryInterface $repository,
    ) {}

    public function handle(DeleteSeccionBloqueCommand $command): void
    {
        $this->repository->delete($command->idSeccionbloque);
    }
}
