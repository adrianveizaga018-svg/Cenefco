<?php

namespace App\Domain\ModulosAcademicos\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface ModuloAcademicoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, ?string $posicion, bool $conInactivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    public function softDelete(int $id): void;
}
