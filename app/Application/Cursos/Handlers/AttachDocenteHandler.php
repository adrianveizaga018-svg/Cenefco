<?php

namespace App\Application\Cursos\Handlers;

use App\Application\Cursos\Commands\AttachDocenteCommand;
use App\Domain\Cursos\Contracts\CursoRepositoryInterface;

class AttachDocenteHandler
{
    public function __construct(private readonly CursoRepositoryInterface $repository) {}

    public function handle(AttachDocenteCommand $c): void
    {
        $this->repository->docentesAttach($c->programa_id, $c->docente_id, $c->orden);
    }
}
