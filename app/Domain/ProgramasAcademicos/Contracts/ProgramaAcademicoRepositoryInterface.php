<?php

namespace App\Domain\ProgramasAcademicos\Contracts;

use App\Application\ProgramasAcademicos\DTOs\ProgramaAcademicoDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface ProgramaAcademicoRepositoryInterface
{
    
    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?int $idTipoprograma): array;

    public function findById(int $id): ProgramaAcademicoDTO;

    public function create(array $data): ProgramaAcademicoDTO;

    public function update(int $id, array $data): ProgramaAcademicoDTO;

    public function delete(int $id): void;

    public function tieneInscritos(int $id): bool;
}
