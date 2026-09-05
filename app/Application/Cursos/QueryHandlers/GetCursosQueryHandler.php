<?php

namespace App\Application\Cursos\QueryHandlers;

use App\Application\Cursos\Queries\GetCursosQuery;
use App\Domain\Cursos\Contracts\CursoRepositoryInterface;

class GetCursosQueryHandler
{
    public function __construct(private readonly CursoRepositoryInterface $repository) {}

    public function handle(GetCursosQuery $q): array
    {
        return $this->repository->paginate($q->pagination, $q->soloPublicados, $q->idsPermitidos);
    }
}
