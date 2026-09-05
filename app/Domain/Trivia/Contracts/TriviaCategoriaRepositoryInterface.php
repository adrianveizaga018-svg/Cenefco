<?php

namespace App\Domain\Trivia\Contracts;

use App\Application\Trivia\DTOs\TriviaCategoriaDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface TriviaCategoriaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array;

    public function findById(int $id): TriviaCategoriaDTO;

    public function findBySlug(string $slug): TriviaCategoriaDTO;

    public function create(array $data): TriviaCategoriaDTO;

    public function update(int $id, array $data): TriviaCategoriaDTO;

    public function delete(int|array $ids): bool;
}
