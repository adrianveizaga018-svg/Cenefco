<?php

namespace App\Application\Cursos\Handlers;

use App\Application\Cursos\Commands\DeleteCursoCommand;
use App\Domain\Cursos\Contracts\CursoRepositoryInterface;
use App\Domain\Cursos\Exceptions\CursoConInscritosException;

class DeleteCursoHandler
{
    public function __construct(private readonly CursoRepositoryInterface $repository) {}

    public function handle(DeleteCursoCommand $c): void
    {
        if ($this->repository->tieneInscritos($c->id)) {
            throw new CursoConInscritosException();
        }

        $this->repository->delete($c->id);
    }
}
