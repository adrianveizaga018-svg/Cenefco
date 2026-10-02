<?php

namespace App\Application\ProgramasAcademicos\QueryHandlers;

use App\Application\ProgramasAcademicos\DTOs\ProgramaAcademicoDTO;
use App\Application\ProgramasAcademicos\Queries\GetProgramaAcademicoByIdQuery;
use App\Domain\ProgramasAcademicos\Contracts\ProgramaAcademicoRepositoryInterface;

class GetProgramaAcademicoByIdQueryHandler
{
    public function __construct(
        private readonly ProgramaAcademicoRepositoryInterface $repository
    ) {}

    public function handle(GetProgramaAcademicoByIdQuery $query): ProgramaAcademicoDTO
    {
        return $this->repository->findById($query->id);
    }
}
