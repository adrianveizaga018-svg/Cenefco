<?php

namespace App\Application\WebMenus\Handlers;

use App\Application\WebMenus\Commands\CreateWebMenuCommand;
use App\Application\WebMenus\DTOs\WebMenuDTO;
use App\Domain\WebMenus\Contracts\WebMenuRepositoryInterface;

class CreateWebMenuHandler
{
    public function __construct(
        private readonly WebMenuRepositoryInterface $repository,
    ) {}

    public function handle(CreateWebMenuCommand $command): WebMenuDTO
    {
        $row = $this->repository->create([
            'nombre'      => $command->nombre,
            'descripcion' => $command->descripcion,
            'activo'      => $command->activo,
        ]);

        return WebMenuDTO::fromRow($row);
    }
}
