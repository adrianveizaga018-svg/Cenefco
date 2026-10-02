<?php

namespace App\Domain\Imparticiones\Contracts;

use App\Application\Imparticiones\DTOs\ImparteDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface ImparteRepositoryInterface
{
    
    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?string $periodo, ?string $gestion, ?int $idMat, ?int $idUs): array;

    public function findById(int $id): ImparteDTO;

    public function create(array $data): ImparteDTO;

    public function update(int $id, array $data): ImparteDTO;

    public function delete(int $id): void;

    public function tieneInscritos(int $id): bool;
}
