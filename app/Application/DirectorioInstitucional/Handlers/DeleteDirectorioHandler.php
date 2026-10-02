<?php

namespace App\Application\DirectorioInstitucional\Handlers;

use App\Application\DirectorioInstitucional\Commands\DeleteDirectorioCommand;
use App\Domain\DirectorioInstitucional\Contracts\DirectorioInstitucionalRepositoryInterface;

class DeleteDirectorioHandler
{
    public function __construct(
        private readonly DirectorioInstitucionalRepositoryInterface $repository
    ) {}

    public function handle(DeleteDirectorioCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
