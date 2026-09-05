<?php

namespace App\Application\CertConfigProgramas\Queries;

final readonly class GetSolicitudesQuery
{
    public function __construct(
        public int $pageSize = 15,
        public int $page = 1,
        public ?string $estado = null,
        public ?int $programa_id = null,
    ) {}
}
