<?php

namespace App\Domain\Ingresos\Contracts;

use App\Shared\Kernel\DTOs\PaginationDTO;

interface IngresoRepositoryInterface
{
    
    public function paginate(PaginationDTO $pagination, ?string $desde, ?string $hasta): array;

    public function resumen(): array;
}
