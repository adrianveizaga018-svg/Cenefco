<?php

namespace App\Application\ListaAprobados\Handlers;

use App\Application\ListaAprobados\Commands\DeleteListaAprobadosBulkCommand;
use App\Domain\ListaAprobados\Contracts\ListaAprobadosRepositoryInterface;

class DeleteListaAprobadosBulkHandler
{
    public function __construct(
        private readonly ListaAprobadosRepositoryInterface $repository
    ) {}

    public function handle(DeleteListaAprobadosBulkCommand $command): int
    {
        return $this->repository->deleteMany($command->ids);
    }
}
