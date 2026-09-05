<?php

namespace App\Domain\MenusAcademicos\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface MenuAcademicoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, ?int $idMod, bool $conInactivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    public function softDelete(int $id): void;
}
