<?php

declare(strict_types=1);

namespace App\Application\CalendarioAcademico\Commands;

final readonly class DeleteCalendarioAcademicoCommand
{
    public function __construct(
        public int|string $id,
    ) {}
}
