<?php

namespace App\Application\Cursos\QueryHandlers;

use App\Application\Cursos\DTOs\CursoDTO;
use App\Application\Cursos\Queries\GetCursoByIdQuery;
use App\Domain\Cursos\Contracts\CursoRepositoryInterface;

class GetCursoByIdQueryHandler
{
    public function __construct(private readonly CursoRepositoryInterface $repository) {}

    public function handle(GetCursoByIdQuery $q): CursoDTO
    {
        return $this->repository->findById($q->id, $q->soloPublicados);
    }
}
