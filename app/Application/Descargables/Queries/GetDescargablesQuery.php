<?php

declare(strict_types=1);

namespace App\Application\Descargables\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetDescargablesQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $soloActivos = false,
    ) {}
}
