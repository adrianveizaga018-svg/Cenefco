<?php

namespace App\Application\Asesores\Queries;

final readonly class GetAsesoresQuery
{
    public function __construct(
        public int $pageIndex,
        public int $pageSize,
        public ?string $query,
        public ?bool $activo,
    ) {}
}
