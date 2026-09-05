<?php

namespace App\Domain\Universidades\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface UniversidadRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, ?int $idCiudad, ?int $idTipoUniversidad): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    public function delete(int $id): void;
}
