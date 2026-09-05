<?php

declare(strict_types=1);

namespace App\Application\CalendarioAcademico\Queries;

final readonly class GetCalendarioAcademicoByIdQuery
{
    public function __construct(
        public int|string $id,
    ) {}
}
