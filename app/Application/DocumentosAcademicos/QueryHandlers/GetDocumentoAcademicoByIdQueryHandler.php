<?php

namespace App\Application\DocumentosAcademicos\QueryHandlers;

use App\Application\DocumentosAcademicos\DTOs\DocumentoAcademicoDTO;
use App\Application\DocumentosAcademicos\Queries\GetDocumentoAcademicoByIdQuery;
use App\Domain\DocumentosAcademicos\Contracts\DocumentoAcademicoRepositoryInterface;

class GetDocumentoAcademicoByIdQueryHandler
{
    public function __construct(
        private readonly DocumentoAcademicoRepositoryInterface $repository
    ) {}

    public function handle(GetDocumentoAcademicoByIdQuery $query): DocumentoAcademicoDTO
    {
        return $this->repository->findById($query->id);
    }
}
