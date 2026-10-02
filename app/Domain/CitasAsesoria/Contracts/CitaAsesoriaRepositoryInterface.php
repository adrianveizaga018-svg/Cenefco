<?php

namespace App\Domain\CitasAsesoria\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface CitaAsesoriaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, ?string $estado): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    public function delete(int $id): void;
}
