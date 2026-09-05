<?php

namespace App\Domain\Notas\Contracts;

use App\Application\Notas\DTOs\NotaDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface NotaRepositoryInterface
{
    
    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?int $idUs, ?int $idImp, ?int $idMat, ?string $periodo, ?string $gestion): array;

    public function findById(int $id): NotaDTO;

    public function create(array $data): NotaDTO;

    public function update(int $id, array $data): NotaDTO;

    public function delete(int $id): void;
}
