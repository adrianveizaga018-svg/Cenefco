<?php

namespace App\Application\Cursos\QueryHandlers;

use App\Application\Cursos\Queries\GetDocentesByCursoQuery;
use App\Domain\Cursos\Contracts\CursoRepositoryInterface;

class GetDocentesByCursoQueryHandler
{
    public function __construct(private readonly CursoRepositoryInterface $repository) {}

    public function handle(GetDocentesByCursoQuery $q): array
    {
        return $this->repository->docentesIndex($q->programa_id, $q->soloVisibles);
    }
}
