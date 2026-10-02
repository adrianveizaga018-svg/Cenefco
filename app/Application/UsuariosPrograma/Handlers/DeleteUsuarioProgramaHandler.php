<?php

namespace App\Application\UsuariosPrograma\Handlers;

use App\Application\UsuariosPrograma\Commands\DeleteUsuarioProgramaCommand;
use App\Domain\UsuariosPrograma\Contracts\UsuarioProgramaRepositoryInterface;

class DeleteUsuarioProgramaHandler
{
    public function __construct(
        private readonly UsuarioProgramaRepositoryInterface $repository,
    ) {}

    public function handle(DeleteUsuarioProgramaCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
