<?php

namespace App\Application\Boletines\Handlers;

use App\Application\Boletines\Commands\DeleteBoletinCommand;
use App\Domain\Boletines\Contracts\BoletinRepositoryInterface;

class DeleteBoletinHandler
{
    public function __construct(private readonly BoletinRepositoryInterface $repository) {}

    public function handle(DeleteBoletinCommand $command): void
    {
        $this->repository->findById($command->id);
        $this->repository->softDelete($command->id);
    }
}
