<?php

namespace App\Application\WebMenuItems\Handlers;

use App\Application\WebMenuItems\Commands\CreateWebMenuItemCommand;
use App\Application\WebMenuItems\DTOs\WebMenuItemDTO;
use App\Domain\WebMenuItems\Contracts\WebMenuItemRepositoryInterface;

class CreateWebMenuItemHandler
{
    public function __construct(
        private readonly WebMenuItemRepositoryInterface $repository,
    ) {}

    public function handle(CreateWebMenuItemCommand $command): WebMenuItemDTO
    {
        $row = $this->repository->create([
            'menu_id'   => $command->menuId,
            'parent_id' => $command->parentId,
            'titulo'    => $command->titulo,
            'url'       => $command->url,
            'orden'     => $command->orden,
            'activo'    => $command->activo,
        ]);

        return WebMenuItemDTO::fromRow($row);
    }
}
