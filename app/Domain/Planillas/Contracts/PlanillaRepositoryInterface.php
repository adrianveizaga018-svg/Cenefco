<?php

namespace App\Domain\Planillas\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface PlanillaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array;
    public function findById(int $id): mixed;
    public function existePlanillaDelMes(int $anio, int $mes): bool;
    public function create(array $data, array $detalle): mixed;
    public function delete(int $id): bool;
}
