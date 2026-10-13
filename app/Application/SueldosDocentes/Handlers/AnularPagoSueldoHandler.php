<?php

namespace App\Application\SueldosDocentes\Handlers;

use App\Application\SueldosDocentes\Commands\AnularPagoSueldoCommand;
use App\Application\Gastos\Services\GastosAutomaticosService;
use App\Domain\SueldosDocentes\Contracts\SueldoDocenteRepositoryInterface;

class AnularPagoSueldoHandler
{
    public function __construct(
        private readonly SueldoDocenteRepositoryInterface $repository,
        private readonly GastosAutomaticosService $gastos,
    ) {}

    public function handle(AnularPagoSueldoCommand $command): void
    {
        $this->repository->anularPago($command->idSueldo, $command->pagoId);
        $this->gastos->alAnularPagoDocente($command->pagoId);
    }
}
