<?php

declare(strict_types=1);

namespace App\Application\CalendarioAcademico\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetCalendarioAcademicoQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool $soloPublicos = false,
        public ?string $tipo = null,
    ) {}
}
