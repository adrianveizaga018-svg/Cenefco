<?php

namespace App\Application\Cursos\QueryHandlers;

use App\Application\Cursos\DTOs\CursoDTO;
use App\Application\Cursos\Queries\GetCursoBySlugQuery;
use App\Domain\Cursos\Contracts\CursoRepositoryInterface;

class GetCursoBySlugQueryHandler
{
    public function __construct(private readonly CursoRepositoryInterface $repository) {}

    public function handle(GetCursoBySlugQuery $q): CursoDTO
    {
        return $this->repository->findBySlug($q->slug, $q->soloPublicados);
    }
}
