<?php

namespace App\Domain\UsuariosPlan\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface UsuarioPlanRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idUs, ?int $idPlan, bool $conInactivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): void;
}
