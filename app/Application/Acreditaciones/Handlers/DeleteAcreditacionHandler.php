<?php

namespace App\Application\Acreditaciones\Handlers;

use App\Application\Acreditaciones\Commands\DeleteAcreditacionCommand;
use App\Domain\Acreditaciones\Contracts\AcreditacionRepositoryInterface;

class DeleteAcreditacionHandler
{
    public function __construct(
        private readonly AcreditacionRepositoryInterface $repository,
    ) {}

    public function handle(DeleteAcreditacionCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
