<?php

namespace App\Domain\DirectorioArchivos\Contracts;

use App\Application\DirectorioArchivos\DTOs\ArchivoParticipanteDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface DirectorioArchivosRepositoryInterface
{
    public function paginateCursos(PaginationDTO $pagination, ?array $idImpPermitidos): array;

    public function paginateParticipantes(int $idImp, PaginationDTO $pagination): array;

    public function archivosDeParticipante(int $idIns): ArchivoParticipanteDTO;
}
