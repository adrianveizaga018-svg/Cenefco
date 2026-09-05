<?php

namespace App\Application\DirectorioInstitucional\Queries;

final readonly class GetDirectorioQuery
{
    public function __construct(
        public int $pageIndex,
        public int $pageSize,
        public string $query,
        public string $activo,
    ) {}
}
