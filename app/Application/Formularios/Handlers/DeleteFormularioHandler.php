<?php

namespace App\Application\Formularios\Handlers;

use App\Application\Formularios\Commands\DeleteFormularioCommand;
use App\Domain\Formularios\Contracts\FormularioRepositoryInterface;

class DeleteFormularioHandler
{
    public function __construct(private readonly FormularioRepositoryInterface $repository) {}

    public function handle(DeleteFormularioCommand $c): bool
    {
        return $this->repository->delete($c->id);
    }
}
