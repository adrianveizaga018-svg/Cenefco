<?php

namespace App\Application\Revistas\Handlers;

use App\Application\Revistas\Commands\DeleteRevistaCommand;
use App\Domain\Revistas\Contracts\RevistaRepositoryInterface;

class DeleteRevistaHandler
{
    public function __construct(private readonly RevistaRepositoryInterface $repository) {}

    public function handle(DeleteRevistaCommand $command): void
    {
        $this->repository->findById($command->id);
        $this->repository->softDelete($command->id);
    }
}
