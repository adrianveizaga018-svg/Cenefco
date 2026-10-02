<?php

namespace App\Application\Materias\Handlers;

use App\Application\Materias\Commands\DeleteMateriaCommand;
use App\Domain\Materias\Contracts\MateriaRepositoryInterface;

class DeleteMateriaHandler
{
    public function __construct(
        private readonly MateriaRepositoryInterface $repository
    ) {}

    public function handle(DeleteMateriaCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
