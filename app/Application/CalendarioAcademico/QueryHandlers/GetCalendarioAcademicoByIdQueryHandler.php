<?php

declare(strict_types=1);

namespace App\Application\CalendarioAcademico\QueryHandlers;

use App\Application\CalendarioAcademico\DTOs\CalendarioAcademicoDTO;
use App\Application\CalendarioAcademico\Queries\GetCalendarioAcademicoByIdQuery;
use App\Domain\CalendarioAcademico\Contracts\CalendarioAcademicoRepositoryInterface;

class GetCalendarioAcademicoByIdQueryHandler
{
    public function __construct(
        private readonly CalendarioAcademicoRepositoryInterface $repository,
    ) {}

    public function handle(GetCalendarioAcademicoByIdQuery $query): CalendarioAcademicoDTO
    {
        return $this->repository->findById($query->id);
    }
}
