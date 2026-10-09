<?php

namespace App\Domain\ConfiguracionesAcademicas\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface ConfiguracionAcademicaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $gestion, ?int $idPlan, bool $conInactivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    /** Baja lógica: pone estado = 0. */
    public function delete(int $id): void;
}
