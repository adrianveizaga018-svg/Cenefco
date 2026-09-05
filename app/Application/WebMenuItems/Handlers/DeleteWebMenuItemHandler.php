<?php

namespace App\Application\WebMenuItems\Handlers;

use App\Application\WebMenuItems\Commands\DeleteWebMenuItemCommand;
use App\Domain\WebMenuItems\Contracts\WebMenuItemRepositoryInterface;

class DeleteWebMenuItemHandler
{
    public function __construct(
        private readonly WebMenuItemRepositoryInterface $repository,
    ) {}

    public function handle(DeleteWebMenuItemCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
