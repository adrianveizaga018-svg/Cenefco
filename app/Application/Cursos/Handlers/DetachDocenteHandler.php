<?php

namespace App\Application\Cursos\Handlers;

use App\Application\Cursos\Commands\DetachDocenteCommand;
use App\Domain\Cursos\Contracts\CursoRepositoryInterface;

class DetachDocenteHandler
{
    public function __construct(private readonly CursoRepositoryInterface $repository) {}

    public function handle(DetachDocenteCommand $c): void
    {
        $this->repository->docentesDetach($c->programa_id, $c->docente_id);
    }
}
