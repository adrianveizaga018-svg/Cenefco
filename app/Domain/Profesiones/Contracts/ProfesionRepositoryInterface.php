<?php

namespace App\Domain\Profesiones\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface ProfesionRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, bool $soloActivos = false): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    public function delete(int $id): void;
}
