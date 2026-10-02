<?php

namespace App\Domain\CategoriasNoticia\Contracts;

use App\Application\CategoriasNoticia\DTOs\CategoriaNoticiaDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface CategoriaNoticiaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array;

    public function findById(int $id): CategoriaNoticiaDTO;

    public function findBySlug(string $slug): CategoriaNoticiaDTO;

    public function create(array $data): CategoriaNoticiaDTO;

    public function update(int $id, array $data): CategoriaNoticiaDTO;

    public function delete(int|array $ids): bool;
}
