<?php

namespace App\Application\Permisos\Handlers;

use App\Application\Permisos\Commands\CreatePermisoCommand;
use App\Application\Permisos\DTOs\PermisoDTO;
use App\Domain\Permisos\Contracts\PermisoRepositoryInterface;

class CreatePermisoHandler
{
    public function __construct(
        private readonly PermisoRepositoryInterface $repository
    ) {}

    public function handle(CreatePermisoCommand $command): PermisoDTO
    {
        $model = $this->repository->create([
            'codigo'      => $command->codigo,
            'descripcion' => $command->descripcion,
            'modulo'      => $command->modulo,
        ]);

        return PermisoDTO::fromModel($model);
    }
}
