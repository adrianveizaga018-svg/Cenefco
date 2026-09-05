<?php

namespace App\Application\ConfiguracionesAcademicas\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetConfiguracionesAcademicasQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int $gestion,
        public ?int $idPlan,
        public bool $conInactivos,
    ) {}
}
