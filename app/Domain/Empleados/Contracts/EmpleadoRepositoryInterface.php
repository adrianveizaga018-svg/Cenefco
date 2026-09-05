<?php

namespace App\Domain\Empleados\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface EmpleadoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array;
    public function findById(int $id): mixed;
    public function findAllActivos(): array;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): bool;
}
