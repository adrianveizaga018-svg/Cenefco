<?php

namespace App\Application\Tesis\Handlers;

use App\Application\Tesis\Commands\DeleteTesisCommand;
use App\Domain\Tesis\Contracts\TesisRepositoryInterface;

class DeleteTesisHandler
{
    public function __construct(private readonly TesisRepositoryInterface $repository) {}

    public function handle(DeleteTesisCommand $command): void
    {
        $this->repository->findById($command->id);
        $this->repository->softDelete($command->id);
    }
}
