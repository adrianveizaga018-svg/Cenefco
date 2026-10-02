<?php

namespace App\Domain\Pagos\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface FechaPagoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, array $filters = []): array;
    public function findById(int $id): mixed;
    public function findByPlan(int $idPlan): array;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function anular(int $id): void;
}
