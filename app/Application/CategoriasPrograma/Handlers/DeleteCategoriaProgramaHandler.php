<?php

namespace App\Application\CategoriasPrograma\Handlers;

use App\Application\CategoriasPrograma\Commands\DeleteCategoriaProgramaCommand;
use App\Domain\CategoriasPrograma\Contracts\CategoriaProgramaRepositoryInterface;

class DeleteCategoriaProgramaHandler
{
    public function __construct(
        private readonly CategoriaProgramaRepositoryInterface $repository,
    ) {}

    public function handle(DeleteCategoriaProgramaCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
