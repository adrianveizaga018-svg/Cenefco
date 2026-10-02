<?php

namespace App\Application\FechasDoc\Handlers;

use App\Application\FechasDoc\Commands\DeleteFechaDocCommand;
use App\Domain\FechasDoc\Contracts\FechaDocRepositoryInterface;

class DeleteFechaDocHandler
{
    public function __construct(
        private readonly FechaDocRepositoryInterface $repository
    ) {}

    public function handle(DeleteFechaDocCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
