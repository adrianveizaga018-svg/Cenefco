<?php

namespace App\Domain\Comisiones\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface ComisionLiquidacionRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, array $filters = []): array;

    public function findById(int $id): mixed;

    public function create(array $data, array $detalle): mixed;

    public function update(int $id, array $data): mixed;

    public function eliminarDetalle(int $id): void;
}
