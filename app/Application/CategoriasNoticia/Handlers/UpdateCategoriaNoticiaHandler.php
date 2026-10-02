<?php

namespace App\Application\CategoriasNoticia\Handlers;

use App\Application\CategoriasNoticia\Commands\UpdateCategoriaNoticiaCommand;
use App\Application\CategoriasNoticia\DTOs\CategoriaNoticiaDTO;
use App\Domain\CategoriasNoticia\Contracts\CategoriaNoticiaRepositoryInterface;

class UpdateCategoriaNoticiaHandler
{
    public function __construct(
        private readonly CategoriaNoticiaRepositoryInterface $repository
    ) {}

    public function handle(UpdateCategoriaNoticiaCommand $command): CategoriaNoticiaDTO
    {
        return $this->repository->update($command->id, $command->data);
    }
}
