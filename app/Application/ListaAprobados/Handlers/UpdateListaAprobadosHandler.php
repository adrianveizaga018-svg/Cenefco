<?php

namespace App\Application\ListaAprobados\Handlers;

use App\Application\ListaAprobados\Commands\UpdateListaAprobadosCommand;
use App\Application\ListaAprobados\DTOs\ListaAprobadosDTO;
use App\Domain\ListaAprobados\Contracts\ListaAprobadosRepositoryInterface;

class UpdateListaAprobadosHandler
{
    public function __construct(
        private readonly ListaAprobadosRepositoryInterface $repository
    ) {}

    public function handle(UpdateListaAprobadosCommand $command): ListaAprobadosDTO
    {
        $changes = $command->changes;
        $changes['updated_at'] = now();

        $row = $this->repository->update($command->id, $changes);

        return ListaAprobadosDTO::fromRow($row);
    }
}
