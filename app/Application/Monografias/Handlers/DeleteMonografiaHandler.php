<?php

namespace App\Application\Monografias\Handlers;

use App\Application\Monografias\Commands\DeleteMonografiaCommand;
use App\Domain\Monografias\Contracts\MonografiaRepositoryInterface;

class DeleteMonografiaHandler
{
    public function __construct(private readonly MonografiaRepositoryInterface $repository) {}

    public function handle(DeleteMonografiaCommand $command): void
    {
        $this->repository->findById($command->id);
        $this->repository->softDelete($command->id);
    }
}
