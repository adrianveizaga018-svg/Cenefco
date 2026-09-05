<?php

namespace App\Application\Acreditaciones\Queries;

final readonly class GetAcreditacionesQuery
{
    public function __construct(
        public int     $page,
        public int     $pageSize,
        public ?string $query,
        public ?bool   $activo,
    ) {}
}
