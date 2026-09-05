<?php

namespace App\Application\DirectorioArchivos\QueryHandlers;

use App\Application\DirectorioArchivos\Queries\GetCursosDirectorioQuery;
use App\Domain\DirectorioArchivos\Contracts\DirectorioArchivosRepositoryInterface;

class GetCursosDirectorioQueryHandler
{
    public function __construct(
        private readonly DirectorioArchivosRepositoryInterface $repository
    ) {}

    public function handle(GetCursosDirectorioQuery $query): array
    {
        return $this->repository->paginateCursos($query->pagination, $query->idImpPermitidos);
    }
}
