<?php

namespace App\Application\CategoriasCampo\Handlers;

use App\Application\CategoriasCampo\Commands\DeleteCategoriaCampoCommand;
use App\Domain\CategoriasCampo\Contracts\CategoriaCampoRepositoryInterface;

class DeleteCategoriaCampoHandler
{
    public function __construct(
        private readonly CategoriaCampoRepositoryInterface $repository,
    ) {}

    public function handle(DeleteCategoriaCampoCommand $command): bool
    {
        return $this->repository->delete($command->categoria_id, $command->id);
    }
}
