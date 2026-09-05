<?php

namespace App\Application\DirectorioArchivos\Queries;

final readonly class GetArchivosParticipanteQuery
{
    public function __construct(public int $idIns) {}
}
