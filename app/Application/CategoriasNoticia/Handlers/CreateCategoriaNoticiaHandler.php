<?php

namespace App\Application\CategoriasNoticia\Handlers;

use App\Application\CategoriasNoticia\Commands\CreateCategoriaNoticiaCommand;
use App\Application\CategoriasNoticia\DTOs\CategoriaNoticiaDTO;
use App\Domain\CategoriasNoticia\Contracts\CategoriaNoticiaRepositoryInterface;

class CreateCategoriaNoticiaHandler
{
    public function __construct(
        private readonly CategoriaNoticiaRepositoryInterface $repository
    ) {}

    public function handle(CreateCategoriaNoticiaCommand $command): CategoriaNoticiaDTO
    {
        return $this->repository->create([
            'nombre' => $command->nombre,
            'descripcion' => $command->descripcion,
            'color_hex' => $command->color_hex,
            'activa' => $command->activa,
        ]);
    }
}
