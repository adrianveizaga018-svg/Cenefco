<?php

namespace App\Domain\SeccionesBloque\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface SeccionBloqueRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idBloqueajustable, bool $conInactivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    public function softDelete(int $id): void;
}
