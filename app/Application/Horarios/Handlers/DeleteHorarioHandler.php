<?php

namespace App\Application\Horarios\Handlers;

use App\Application\Horarios\Commands\DeleteHorarioCommand;
use App\Domain\Horarios\Contracts\HorarioRepositoryInterface;

class DeleteHorarioHandler
{
    public function __construct(
        private readonly HorarioRepositoryInterface $repository
    ) {}

    public function handle(DeleteHorarioCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
