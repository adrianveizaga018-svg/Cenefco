<?php

declare(strict_types=1);

namespace App\Application\CalendarioAcademico\QueryHandlers;

use App\Application\CalendarioAcademico\Queries\GetCursosVigentesQuery;
use App\Domain\CalendarioAcademico\Contracts\CalendarioAcademicoRepositoryInterface;

class GetCursosVigentesQueryHandler
{
    public function __construct(
        private readonly CalendarioAcademicoRepositoryInterface $repository,
    ) {}

    public function handle(GetCursosVigentesQuery $query): array
    {
        return $this->repository->cursosVigentes();
    }
}
