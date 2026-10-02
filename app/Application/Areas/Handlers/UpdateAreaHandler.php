<?php

namespace App\Application\Areas\Handlers;

use App\Application\Areas\Commands\UpdateAreaCommand;
use App\Application\Areas\DTOs\AreaDTO;
use App\Domain\Areas\Contracts\AreaRepositoryInterface;

class UpdateAreaHandler
{
    public function __construct(
        private readonly AreaRepositoryInterface $repository,
    ) {}

    public function handle(UpdateAreaCommand $command): AreaDTO
    {
        return $this->repository->update($command->id, $command->data);
    }
}
