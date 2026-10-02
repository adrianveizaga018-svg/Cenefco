<?php

namespace App\Application\UsuariosTipoPrograma\Handlers;

use App\Application\UsuariosTipoPrograma\Commands\DeleteUsuarioTipoProgramaCommand;
use App\Domain\UsuariosTipoPrograma\Contracts\UsuarioTipoProgramaRepositoryInterface;

class DeleteUsuarioTipoProgramaHandler
{
    public function __construct(
        private readonly UsuarioTipoProgramaRepositoryInterface $repository,
    ) {}

    public function handle(DeleteUsuarioTipoProgramaCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
