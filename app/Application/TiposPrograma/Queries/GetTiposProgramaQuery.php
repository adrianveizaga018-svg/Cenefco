<?php

namespace App\Application\TiposPrograma\Queries;

final readonly class GetTiposProgramaQuery
{
    public function __construct(
        public int $pageIndex,
        public int $pageSize,
        public ?string $query,
        public bool $conInactivos,
    ) {}
}
