<?php

namespace App\Application\Resenas\QueryHandlers;

use App\Application\Resenas\Queries\GetEstudiantesProgramaQuery;
use App\Domain\Resenas\Contracts\ResenaRepositoryInterface;

class GetEstudiantesProgramaQueryHandler
{
    public function __construct(
        private readonly ResenaRepositoryInterface $repository,
    ) {}

    public function handle(GetEstudiantesProgramaQuery $query): array
    {
        return $this->repository->estudiantesPrograma($query->programaId);
    }
}
