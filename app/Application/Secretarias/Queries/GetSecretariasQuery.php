<?php

namespace App\Application\Secretarias\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetSecretariasQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $soloActivos = false,
    ) {}
}
