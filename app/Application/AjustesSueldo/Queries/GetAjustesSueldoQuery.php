<?php

namespace App\Application\AjustesSueldo\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetAjustesSueldoQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int $empleadoId = null,
        public ?int $anio = null,
        public ?int $mes = null,
        public ?bool $aplicado = null,
    ) {}
}
