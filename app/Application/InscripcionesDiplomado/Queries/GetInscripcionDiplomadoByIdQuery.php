<?php

namespace App\Application\InscripcionesDiplomado\Queries;

final readonly class GetInscripcionDiplomadoByIdQuery
{
    public function __construct(public int $id) {}
}
