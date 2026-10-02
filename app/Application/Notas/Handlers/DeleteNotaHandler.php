<?php

namespace App\Application\Notas\Handlers;

use App\Application\Notas\Commands\DeleteNotaCommand;
use App\Domain\Notas\Contracts\NotaRepositoryInterface;

class DeleteNotaHandler
{
    public function __construct(
        private readonly NotaRepositoryInterface $repository
    ) {}

    public function handle(DeleteNotaCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
