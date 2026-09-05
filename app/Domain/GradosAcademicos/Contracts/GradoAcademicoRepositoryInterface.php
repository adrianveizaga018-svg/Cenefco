<?php

namespace App\Domain\GradosAcademicos\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface GradoAcademicoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, bool $soloActivos = false): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    public function delete(int $id): void;
}
