<?php

namespace App\Application\SugerenciasReclamos\Handlers;

use App\Application\SugerenciasReclamos\Commands\DeleteSugerenciaReclamoCommand;
use App\Domain\SugerenciasReclamos\Contracts\SugerenciaReclamoRepositoryInterface;

class DeleteSugerenciaReclamoHandler
{
    public function __construct(private readonly SugerenciaReclamoRepositoryInterface $repository) {}

    public function handle(DeleteSugerenciaReclamoCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
