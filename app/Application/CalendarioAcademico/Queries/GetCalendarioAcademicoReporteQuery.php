<?php

declare(strict_types=1);

namespace App\Application\CalendarioAcademico\Queries;

final readonly class GetCalendarioAcademicoReporteQuery
{
    public function __construct(
        public string $query = '',
        public ?string $tipo = null,
        public bool $soloPublicos = false,
    ) {}
}
