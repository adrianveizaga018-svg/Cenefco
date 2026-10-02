<?php

namespace App\Application\Convenios\Handlers;

use App\Application\Convenios\Commands\DeleteConvenioCommand;
use App\Domain\Convenios\Contracts\ConvenioRepositoryInterface;

class DeleteConvenioHandler
{
    public function __construct(
        private readonly ConvenioRepositoryInterface $repository,
    ) {}

    public function handle(DeleteConvenioCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
