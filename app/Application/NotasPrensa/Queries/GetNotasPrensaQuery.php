<?php

declare(strict_types=1);

namespace App\Application\NotasPrensa\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetNotasPrensaQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $soloDestacadas = false,
        public bool $soloActivos = false,
    ) {}
}
