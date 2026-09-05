<?php

namespace App\Application\Permisos\Handlers;

use App\Application\Permisos\Commands\DeletePermisoCommand;
use App\Domain\Permisos\Contracts\PermisoRepositoryInterface;

class DeletePermisoHandler
{
    public function __construct(
        private readonly PermisoRepositoryInterface $repository
    ) {}

    public function handle(DeletePermisoCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
