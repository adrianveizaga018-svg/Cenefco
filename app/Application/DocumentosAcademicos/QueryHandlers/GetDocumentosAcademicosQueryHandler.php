<?php

namespace App\Application\DocumentosAcademicos\QueryHandlers;

use App\Application\DocumentosAcademicos\Queries\GetDocumentosAcademicosQuery;
use App\Domain\DocumentosAcademicos\Contracts\DocumentoAcademicoRepositoryInterface;

class GetDocumentosAcademicosQueryHandler
{
    public function __construct(
        private readonly DocumentoAcademicoRepositoryInterface $repository
    ) {}

    public function handle(GetDocumentosAcademicosQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->conInactivos,
            $query->idUs,
            $query->idFechapago,
            $query->idFechadoc,
        );
    }
}
