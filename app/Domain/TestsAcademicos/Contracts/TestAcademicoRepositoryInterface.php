<?php

namespace App\Domain\TestsAcademicos\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface TestAcademicoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, ?bool $habilitarTest, bool $conInactivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    /** Baja lógica (estado = 0). */
    public function delete(int $id): void;
}
