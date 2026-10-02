<?php

namespace App\Application\Usuarios\Handlers;

use App\Application\Usuarios\Commands\DeleteUsuarioCommand;
use App\Domain\Usuarios\Contracts\UserRepositoryInterface;

class DeleteUsuarioHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository
    ) {}

    public function handle(DeleteUsuarioCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
