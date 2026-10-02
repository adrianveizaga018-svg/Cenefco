<?php

namespace App\Application\AjustesSueldo\Handlers;

use App\Application\AjustesSueldo\Commands\DeleteAjusteSueldoCommand;
use App\Domain\AjustesSueldo\Contracts\AjusteSueldoRepositoryInterface;
use App\Domain\AjustesSueldo\Exceptions\AjusteSueldoNotFoundException;
use App\Domain\AjustesSueldo\Exceptions\AjusteSueldoYaAplicadoException;

class DeleteAjusteSueldoHandler
{
    public function __construct(
        private readonly AjusteSueldoRepositoryInterface $repository,
    ) {}

    public function handle(DeleteAjusteSueldoCommand $command): bool
    {
        $ajuste = $this->repository->findById($command->id);
        if (! $ajuste) {
            throw new AjusteSueldoNotFoundException($command->id);
        }

        if ($ajuste->aplicado) {
            throw new AjusteSueldoYaAplicadoException();
        }

        return $this->repository->delete($command->id);
    }
}
