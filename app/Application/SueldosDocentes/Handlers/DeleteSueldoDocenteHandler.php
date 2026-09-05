<?php

namespace App\Application\SueldosDocentes\Handlers;

use App\Application\SueldosDocentes\Commands\DeleteSueldoDocenteCommand;
use App\Domain\SueldosDocentes\Contracts\SueldoDocenteRepositoryInterface;

class DeleteSueldoDocenteHandler
{
    public function __construct(
        private readonly SueldoDocenteRepositoryInterface $repository,
    ) {}

    public function handle(DeleteSueldoDocenteCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
