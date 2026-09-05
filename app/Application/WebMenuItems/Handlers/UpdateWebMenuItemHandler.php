<?php

namespace App\Application\WebMenuItems\Handlers;

use App\Application\WebMenuItems\Commands\UpdateWebMenuItemCommand;
use App\Domain\WebMenuItems\Contracts\WebMenuItemRepositoryInterface;

class UpdateWebMenuItemHandler
{
    public function __construct(
        private readonly WebMenuItemRepositoryInterface $repository,
    ) {}

    public function handle(UpdateWebMenuItemCommand $command): void
    {
        $this->repository->update($command->id, array_filter([
            'menu_id'   => $command->menuId,
            'parent_id' => $command->parentId,
            'titulo'    => $command->titulo,
            'url'       => $command->url,
            'orden'     => $command->orden,
            'activo'    => $command->activo,
        ], fn ($v) => $v !== null));
    }
}
