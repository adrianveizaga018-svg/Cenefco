<?php

namespace App\Application\CategoriasCampo\Handlers;

use App\Application\CategoriasCampo\Commands\ReorderCategoriaCampoCommand;
use App\Domain\CategoriasCampo\Contracts\CategoriaCampoRepositoryInterface;

class ReorderCategoriaCampoHandler
{
    public function __construct(
        private readonly CategoriaCampoRepositoryInterface $repository,
    ) {}

    public function handle(ReorderCategoriaCampoCommand $command): void
    {
        $this->repository->reorder($command->categoria_id, $command->items);
    }
}
