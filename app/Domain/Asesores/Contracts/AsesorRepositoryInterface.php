<?php

namespace App\Domain\Asesores\Contracts;

use App\Application\Asesores\DTOs\AsesorDTO;

interface AsesorRepositoryInterface
{
    public function paginate(int $pageIndex, int $pageSize, ?string $query, ?bool $activo): array;
    public function findById(int $id): object;
    public function create(array $data): object;
    public function update(int $id, array $data): void;
    public function delete(int $id): void;
    public function existsByTelefono(string $telefono, ?int $excludeId): bool;
}
