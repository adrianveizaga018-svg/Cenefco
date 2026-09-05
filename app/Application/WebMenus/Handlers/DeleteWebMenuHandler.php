<?php

namespace App\Application\WebMenus\Handlers;

use App\Application\WebMenus\Commands\DeleteWebMenuCommand;
use App\Domain\WebMenus\Contracts\WebMenuRepositoryInterface;

class DeleteWebMenuHandler
{
    public function __construct(
        private readonly WebMenuRepositoryInterface $repository,
    ) {}

    public function handle(DeleteWebMenuCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
