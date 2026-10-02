<?php

namespace App\Application\Resenas\Handlers;

use App\Application\Resenas\Commands\DeleteResenaCommand;
use App\Domain\Resenas\Contracts\ResenaRepositoryInterface;

class DeleteResenaHandler
{
    public function __construct(
        private readonly ResenaRepositoryInterface $repository,
    ) {}

    public function handle(DeleteResenaCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
