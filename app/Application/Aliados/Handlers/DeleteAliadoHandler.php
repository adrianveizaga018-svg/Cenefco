<?php

namespace App\Application\Aliados\Handlers;

use App\Application\Aliados\Commands\DeleteAliadoCommand;
use App\Domain\Aliados\Contracts\AliadoRepositoryInterface;

class DeleteAliadoHandler
{
    public function __construct(private readonly AliadoRepositoryInterface $repository) {}

    public function handle(DeleteAliadoCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
