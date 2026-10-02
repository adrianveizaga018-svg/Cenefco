<?php

namespace App\Domain\ConfiguracionesAcademicas\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface ConfiguracionAcademicaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $gestion, ?int $idPlan, bool $conInactivos): array;
    public function findById(int $id): mixed;
    public function create(array $data): mixed;
    public function update(int $id, array $data): void;
    public function softDelete(int $id): void;
}
