<?php

namespace App\Domain\CorreosEnviados\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface CorreoEnviadoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $referenciaTipo = null, ?int $referenciaId = null): array;

    public function create(array $data): mixed;
}
