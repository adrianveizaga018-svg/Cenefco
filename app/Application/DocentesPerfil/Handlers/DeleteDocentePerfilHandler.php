<?php

namespace App\Application\DocentesPerfil\Handlers;

use App\Application\DocentesPerfil\Commands\DeleteDocentePerfilCommand;
use App\Domain\DocentesPerfil\Contracts\DocentePerfilRepositoryInterface;

class DeleteDocentePerfilHandler
{
    public function __construct(private readonly DocentePerfilRepositoryInterface $repository) {}

    public function handle(DeleteDocentePerfilCommand $c): void
    {
        $this->repository->delete($c->id);
    }
}
