<?php

namespace App\Domain\DocumentosAcademicos\Contracts;

use App\Application\DocumentosAcademicos\DTOs\DocumentoAcademicoDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface DocumentoAcademicoRepositoryInterface
{
    
    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?int $idUs, ?int $idFechapago, ?int $idFechadoc): array;

    public function findById(int $id): DocumentoAcademicoDTO;

    public function create(array $data): DocumentoAcademicoDTO;

    public function update(int $id, array $data): DocumentoAcademicoDTO;

    public function delete(int $id): void;
}
