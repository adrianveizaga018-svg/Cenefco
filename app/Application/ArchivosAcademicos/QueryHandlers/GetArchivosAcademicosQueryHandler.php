<?php

namespace App\Application\ArchivosAcademicos\QueryHandlers;

use App\Application\ArchivosAcademicos\Queries\GetArchivosAcademicosQuery;
use App\Domain\ArchivosAcademicos\Contracts\ArchivoAcademicoRepositoryInterface;

class GetArchivosAcademicosQueryHandler
{
    public function __construct(
        private readonly ArchivoAcademicoRepositoryInterface $repository,
    ) {}

    public function handle(GetArchivosAcademicosQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->conInactivos);
    }
}
