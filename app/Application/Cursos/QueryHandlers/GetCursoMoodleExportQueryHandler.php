<?php

namespace App\Application\Cursos\QueryHandlers;

use App\Application\Cursos\DTOs\EstudianteMoodleDTO;
use App\Application\Cursos\Queries\GetCursoMoodleExportQuery;
use App\Domain\Cursos\Contracts\CursoRepositoryInterface;

class GetCursoMoodleExportQueryHandler
{
    public function __construct(
        private readonly CursoRepositoryInterface $repository
    ) {}

    public function handle(GetCursoMoodleExportQuery $query): array
    {
        $filas = $this->repository->estudiantesParaExportMoodle($query->idImp);

        return array_map(
            fn (object $row) => EstudianteMoodleDTO::fromRow($row),
            $filas
        );
    }
}
