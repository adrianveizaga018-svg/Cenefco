<?php

namespace App\Domain\TiposNorma\Contracts;

use App\Application\TiposNorma\DTOs\TipoNormaDTO;

interface TipoNormaRepositoryInterface
{
    public function all(): array;

    public function findById(int $id): TipoNormaDTO;

    public function findBySlug(string $slug): TipoNormaDTO;

    public function create(array $data): TipoNormaDTO;

    public function update(int $id, array $data): TipoNormaDTO;

    public function delete(int|array $ids): bool;
}
