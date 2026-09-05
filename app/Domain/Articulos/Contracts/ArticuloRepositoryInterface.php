<?php

namespace App\Domain\Articulos\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface ArticuloRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, ?string $estadoWeb, bool $conInactivos): array;
    public function findById(int $id): mixed;
    public function findBySlug(string $slug): mixed;
    public function create(array $data, array $etiquetaIds): mixed;
    public function update(int $id, array $data, ?array $etiquetaIds): mixed;
    public function softDelete(int $id): void;
    public function uniqueSlug(string $base, ?int $excludeId = null): string;
}
