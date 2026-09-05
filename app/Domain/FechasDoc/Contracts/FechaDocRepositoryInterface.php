<?php

namespace App\Domain\FechasDoc\Contracts;

use App\Application\FechasDoc\DTOs\FechaDocDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface FechaDocRepositoryInterface
{
    
    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?int $idPlandoc): array;

    public function findById(int $id): FechaDocDTO;

    public function create(array $data): FechaDocDTO;

    public function update(int $id, array $data): FechaDocDTO;

    public function delete(int $id): void;
}
