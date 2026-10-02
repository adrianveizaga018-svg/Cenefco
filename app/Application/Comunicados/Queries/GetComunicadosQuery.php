<?php

namespace App\Application\Comunicados\Queries;

final readonly class GetComunicadosQuery
{
    public function __construct(
        public int $pageIndex = 1,
        public int $pageSize = 10,
        public string $query = '',
        public string $estado = '',
        public bool $soloActivos = false,
    ) {}
}
