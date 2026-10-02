<?php

namespace App\Application\CategoriasNoticia\Handlers;

use App\Application\CategoriasNoticia\Commands\DeleteCategoriaNoticiaCommand;
use App\Domain\CategoriasNoticia\Contracts\CategoriaNoticiaRepositoryInterface;

class DeleteCategoriaNoticiaHandler
{
    public function __construct(
        private readonly CategoriaNoticiaRepositoryInterface $repository
    ) {}

    public function handle(DeleteCategoriaNoticiaCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
