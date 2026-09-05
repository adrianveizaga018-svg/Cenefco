<?php

namespace App\Application\ListaAprobados\Handlers;

use App\Application\ListaAprobados\Commands\DeleteListaAprobadosCommand;
use App\Domain\ListaAprobados\Contracts\ListaAprobadosRepositoryInterface;

class DeleteListaAprobadosHandler
{
    public function __construct(
        private readonly ListaAprobadosRepositoryInterface $repository
    ) {}

    public function handle(DeleteListaAprobadosCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
