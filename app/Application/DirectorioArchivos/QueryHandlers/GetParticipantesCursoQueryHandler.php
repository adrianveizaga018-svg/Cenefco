<?php

namespace App\Application\DirectorioArchivos\QueryHandlers;

use App\Application\DirectorioArchivos\Queries\GetParticipantesCursoQuery;
use App\Domain\DirectorioArchivos\Contracts\DirectorioArchivosRepositoryInterface;

class GetParticipantesCursoQueryHandler
{
    public function __construct(
        private readonly DirectorioArchivosRepositoryInterface $repository
    ) {}

    public function handle(GetParticipantesCursoQuery $query): array
    {
        return $this->repository->paginateParticipantes($query->idImp, $query->pagination);
    }
}
