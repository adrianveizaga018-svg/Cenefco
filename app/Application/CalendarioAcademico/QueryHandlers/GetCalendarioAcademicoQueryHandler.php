<?php

declare(strict_types=1);

namespace App\Application\CalendarioAcademico\QueryHandlers;

use App\Application\CalendarioAcademico\Queries\GetCalendarioAcademicoQuery;
use App\Domain\CalendarioAcademico\Contracts\CalendarioAcademicoRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class GetCalendarioAcademicoQueryHandler
{
    public function __construct(
        private readonly CalendarioAcademicoRepositoryInterface $repository,
    ) {}

    public function handle(GetCalendarioAcademicoQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query->pagination, $query->soloPublicos, $query->tipo);
    }
}
