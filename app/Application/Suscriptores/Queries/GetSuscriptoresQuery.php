<?php

declare(strict_types=1);

namespace App\Application\Suscriptores\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetSuscriptoresQuery
{
    public function __construct(
        public PaginationDTO $pagination,
    ) {}
}
