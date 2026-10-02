<?php

namespace App\Application\Areas\Handlers;

use App\Application\Areas\Commands\DeleteAreaCommand;
use App\Domain\Areas\Contracts\AreaRepositoryInterface;

class DeleteAreaHandler
{
    public function __construct(
        private readonly AreaRepositoryInterface $repository,
    ) {}

    public function handle(DeleteAreaCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
