<?php

namespace App\Application\UsuariosAcademicos\Handlers;

use App\Application\UsuariosAcademicos\Commands\DeleteUsuarioAcademicoCommand;
use App\Domain\UsuariosAcademicos\Contracts\UsuarioAcademicoRepositoryInterface;

class DeleteUsuarioAcademicoHandler
{
    public function __construct(
        private readonly UsuarioAcademicoRepositoryInterface $repository
    ) {}

    public function handle(DeleteUsuarioAcademicoCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
