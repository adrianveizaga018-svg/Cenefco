<?php

namespace App\Application\UsuariosPlan\Handlers;

use App\Application\UsuariosPlan\Commands\DeleteUsuarioPlanCommand;
use App\Domain\UsuariosPlan\Contracts\UsuarioPlanRepositoryInterface;

class DeleteUsuarioPlanHandler
{
    public function __construct(
        private readonly UsuarioPlanRepositoryInterface $repository,
    ) {}

    public function handle(DeleteUsuarioPlanCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
