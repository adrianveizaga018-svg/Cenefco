<?php

namespace App\Application\Permisos\Handlers;

use App\Application\Permisos\Commands\UpdatePermisoCommand;
use App\Application\Permisos\DTOs\PermisoDTO;
use App\Domain\Permisos\Contracts\PermisoRepositoryInterface;

class UpdatePermisoHandler
{
    public function __construct(
        private readonly PermisoRepositoryInterface $repository
    ) {}

    public function handle(UpdatePermisoCommand $command): PermisoDTO
    {
        return $this->repository->update($command->id, $command->data);
    }
}
