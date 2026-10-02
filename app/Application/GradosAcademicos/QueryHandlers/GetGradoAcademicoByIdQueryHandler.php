<?php

namespace App\Application\GradosAcademicos\QueryHandlers;

use App\Application\GradosAcademicos\DTOs\GradoAcademicoDTO;
use App\Application\GradosAcademicos\Queries\GetGradoAcademicoByIdQuery;
use App\Domain\GradosAcademicos\Contracts\GradoAcademicoRepositoryInterface;

class GetGradoAcademicoByIdQueryHandler
{
    public function __construct(private readonly GradoAcademicoRepositoryInterface $repository) {}

    public function handle(GetGradoAcademicoByIdQuery $query): GradoAcademicoDTO
    {
        return GradoAcademicoDTO::fromRow($this->repository->findById($query->id));
    }
}
