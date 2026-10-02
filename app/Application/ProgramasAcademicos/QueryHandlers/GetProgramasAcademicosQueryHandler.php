<?php

namespace App\Application\ProgramasAcademicos\QueryHandlers;

use App\Application\ProgramasAcademicos\Queries\GetProgramasAcademicosQuery;
use App\Domain\ProgramasAcademicos\Contracts\ProgramaAcademicoRepositoryInterface;

class GetProgramasAcademicosQueryHandler
{
    public function __construct(
        private readonly ProgramaAcademicoRepositoryInterface $repository
    ) {}

    public function handle(GetProgramasAcademicosQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->conInactivos,
            $query->idTipoprograma,
        );
    }
}
