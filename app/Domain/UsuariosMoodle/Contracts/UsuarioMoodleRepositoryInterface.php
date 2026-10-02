<?php

namespace App\Domain\UsuariosMoodle\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface UsuarioMoodleRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idUs, ?int $idMoodle, bool $conInactivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): void;
}
