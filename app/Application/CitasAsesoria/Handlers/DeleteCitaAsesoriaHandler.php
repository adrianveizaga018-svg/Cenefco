<?php

namespace App\Application\CitasAsesoria\Handlers;

use App\Application\CitasAsesoria\Commands\DeleteCitaAsesoriaCommand;
use App\Domain\CitasAsesoria\Contracts\CitaAsesoriaRepositoryInterface;

class DeleteCitaAsesoriaHandler
{
    public function __construct(
        private readonly CitaAsesoriaRepositoryInterface $repository,
    ) {}

    public function handle(DeleteCitaAsesoriaCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
