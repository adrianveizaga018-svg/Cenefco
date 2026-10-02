<?php

namespace App\Application\Ayudas\Handlers;

use App\Application\Ayudas\Commands\DeleteAyudaCommand;
use App\Domain\Ayudas\Contracts\AyudaRepositoryInterface;

class DeleteAyudaHandler
{
    public function __construct(private readonly AyudaRepositoryInterface $repository) {}

    public function handle(DeleteAyudaCommand $command): void
    {
        $this->repository->findById($command->id);
        $this->repository->softDelete($command->id);
    }
}
