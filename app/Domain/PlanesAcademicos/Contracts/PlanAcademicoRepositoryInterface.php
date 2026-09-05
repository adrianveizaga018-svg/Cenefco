<?php

namespace App\Domain\PlanesAcademicos\Contracts;

use App\Application\PlanesAcademicos\DTOs\PlanAcademicoDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface PlanAcademicoRepositoryInterface
{
    
    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?int $idCatplan, ?int $idMat): array;

    public function findById(int $id): PlanAcademicoDTO;

    public function create(array $data): PlanAcademicoDTO;

    public function update(int $id, array $data): PlanAcademicoDTO;

    public function delete(int $id): void;

    public function siguienteIdDisponible(): int;
}
