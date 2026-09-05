<?php

namespace App\Domain\HojasEvaluacion\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface HojaEvaluacionRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idUs, ?int $idUsTut, bool $conInactivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    public function softDelete(int $id): void;
}
