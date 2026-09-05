<?php

namespace App\Application\GradosAcademicos\QueryHandlers;

use App\Application\GradosAcademicos\Queries\GetGradosAcademicosQuery;
use App\Domain\GradosAcademicos\Contracts\GradoAcademicoRepositoryInterface;

class GetGradosAcademicosQueryHandler
{
    public function __construct(private readonly GradoAcademicoRepositoryInterface $repository) {}

    public function handle(GetGradosAcademicosQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->query, $query->soloActivos);
    }
}
