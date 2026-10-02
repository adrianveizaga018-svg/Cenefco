<?php

namespace App\Application\TiposPostgrado\Handlers;

use App\Application\TiposPostgrado\Commands\DeleteTipoPostgradoCommand;
use App\Domain\TiposPostgrado\Contracts\TipoPostgradoRepositoryInterface;

class DeleteTipoPostgradoHandler
{
    public function __construct(private readonly TipoPostgradoRepositoryInterface $repository) {}

    public function handle(DeleteTipoPostgradoCommand $command): void
    {
        $this->repository->delete($command->idTipopost);
    }
}
