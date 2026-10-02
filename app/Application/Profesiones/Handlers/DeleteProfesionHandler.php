<?php

namespace App\Application\Profesiones\Handlers;

use App\Application\Profesiones\Commands\DeleteProfesionCommand;
use App\Domain\Profesiones\Contracts\ProfesionRepositoryInterface;

class DeleteProfesionHandler
{
    public function __construct(private readonly ProfesionRepositoryInterface $repository) {}

    public function handle(DeleteProfesionCommand $command): void
    {
        $this->repository->findById($command->id);
        $this->repository->delete($command->id);
    }
}
