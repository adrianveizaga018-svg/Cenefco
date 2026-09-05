<?php

namespace App\Application\SueldosDocentes\Handlers;

use App\Application\SueldosDocentes\Commands\RegistrarPagoSueldoLoteCommand;
use App\Application\SueldosDocentes\DTOs\PagoSueldoDTO;
use App\Domain\SueldosDocentes\Contracts\SueldoDocenteRepositoryInterface;

class RegistrarPagoSueldoLoteHandler
{
    public function __construct(
        private readonly SueldoDocenteRepositoryInterface $repository,
    ) {}

    public function handle(RegistrarPagoSueldoLoteCommand $command): array
    {
        return $this->repository->registrarPagoLote($command->idSueldo, $command->cuotas);
    }
}
