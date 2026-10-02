<?php

namespace App\Domain\Normas\Contracts;

use App\Application\Normas\DTOs\NormaDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface NormaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, array $filters = []): array;

    public function findById(int $id): NormaDTO;

    public function findBySlug(string $slug): NormaDTO;

    public function create(array $data): NormaDTO;

    public function update(int $id, array $data): NormaDTO;

    public function delete(int|array $ids): bool;
}
