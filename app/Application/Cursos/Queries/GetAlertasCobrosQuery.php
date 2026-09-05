<?php

namespace App\Application\Cursos\Queries;

final readonly class GetAlertasCobrosQuery
{
    public function __construct(
        public int $diasProximos = 3,
        public ?array $idImpPermitidos = null,
    ) {}
}
