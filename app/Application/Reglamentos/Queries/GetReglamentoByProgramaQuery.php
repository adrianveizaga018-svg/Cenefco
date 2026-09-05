<?php

namespace App\Application\Reglamentos\Queries;

final readonly class GetReglamentoByProgramaQuery
{
    public function __construct(public int $idPrograma) {}
}
