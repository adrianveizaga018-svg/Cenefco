<?php

namespace App\Application\WebMenus\Handlers;

use App\Application\WebMenus\Commands\UpdateWebMenuCommand;
use App\Domain\WebMenus\Contracts\WebMenuRepositoryInterface;

class UpdateWebMenuHandler
{
    public function __construct(
        private readonly WebMenuRepositoryInterface $repository,
    ) {}

    public function handle(UpdateWebMenuCommand $command): void
    {
        $this->repository->update($command->id, array_filter([
            'nombre'      => $command->nombre,
            'descripcion' => $command->descripcion,
            'activo'      => $command->activo,
        ], fn ($v) => $v !== null));
    }
}
