<?php

namespace App\Application\CorreosEnviados\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCorreosEnviadosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string $referenciaTipo = null,
        public ?int $referenciaId = null,
    ) {}
}
