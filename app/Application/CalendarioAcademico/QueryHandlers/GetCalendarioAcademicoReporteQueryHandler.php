<?php

declare(strict_types=1);

namespace App\Application\CalendarioAcademico\QueryHandlers;

use App\Application\CalendarioAcademico\Queries\GetCalendarioAcademicoReporteQuery;
use App\Domain\CalendarioAcademico\Contracts\CalendarioAcademicoRepositoryInterface;

class GetCalendarioAcademicoReporteQueryHandler
{
    public function __construct(
        private readonly CalendarioAcademicoRepositoryInterface $repository,
    ) {}

    public function handle(GetCalendarioAcademicoReporteQuery $query): array
    {
        return $this->repository->paraReporte($query->query, $query->tipo, $query->soloPublicos);
    }
}
