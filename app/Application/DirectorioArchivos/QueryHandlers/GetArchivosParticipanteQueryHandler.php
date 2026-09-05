<?php

namespace App\Application\DirectorioArchivos\QueryHandlers;

use App\Application\DirectorioArchivos\DTOs\ArchivoParticipanteDTO;
use App\Application\DirectorioArchivos\Queries\GetArchivosParticipanteQuery;
use App\Domain\DirectorioArchivos\Contracts\DirectorioArchivosRepositoryInterface;

class GetArchivosParticipanteQueryHandler
{
    public function __construct(
        private readonly DirectorioArchivosRepositoryInterface $repository
    ) {}

    public function handle(GetArchivosParticipanteQuery $query): ArchivoParticipanteDTO
    {
        return $this->repository->archivosDeParticipante($query->idIns);
    }
}
