<?php

namespace App\Domain\Gastos\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface GastoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, array $filtros = []): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): mixed;
    public function delete(int $id): bool;

    public function resumenMes(int $anio, int $mes): array;
    public function resumenPorLineaNegocio(int $anio, int $mes): array;
}
