<?php

namespace App\Application\ArchivosAcademicos\QueryHandlers;

use App\Application\ArchivosAcademicos\DTOs\ArchivoAcademicoDTO;
use App\Application\ArchivosAcademicos\Queries\GetArchivoAcademicoByIdQuery;
use App\Domain\ArchivosAcademicos\Contracts\ArchivoAcademicoRepositoryInterface;

class GetArchivoAcademicoByIdQueryHandler
{
    public function __construct(
        private readonly ArchivoAcademicoRepositoryInterface $repository,
    ) {}

    public function handle(GetArchivoAcademicoByIdQuery $query): ArchivoAcademicoDTO
    {
        return ArchivoAcademicoDTO::fromRow($this->repository->findById($query->idArch));
    }
}
