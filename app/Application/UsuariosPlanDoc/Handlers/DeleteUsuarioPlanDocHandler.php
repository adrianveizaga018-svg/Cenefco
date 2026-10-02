<?php

namespace App\Application\UsuariosPlanDoc\Handlers;

use App\Application\UsuariosPlanDoc\Commands\DeleteUsuarioPlanDocCommand;
use App\Domain\UsuariosPlanDoc\Contracts\UsuarioPlanDocRepositoryInterface;

class DeleteUsuarioPlanDocHandler
{
    public function __construct(
        private readonly UsuarioPlanDocRepositoryInterface $repository,
    ) {}

    public function handle(DeleteUsuarioPlanDocCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
