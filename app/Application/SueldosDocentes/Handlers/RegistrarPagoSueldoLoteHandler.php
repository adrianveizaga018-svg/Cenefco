<?php

namespace App\Application\SueldosDocentes\Handlers;

use App\Application\SueldosDocentes\Commands\RegistrarPagoSueldoLoteCommand;
use App\Application\SueldosDocentes\DTOs\PagoSueldoDTO;
use App\Application\Gastos\Services\GastosAutomaticosService;
use App\Domain\SueldosDocentes\Contracts\SueldoDocenteRepositoryInterface;

class RegistrarPagoSueldoLoteHandler
{
    public function __construct(
        private readonly SueldoDocenteRepositoryInterface $repository,
        private readonly GastosAutomaticosService $gastos,
    ) {}

    public function handle(RegistrarPagoSueldoLoteCommand $command): array
    {
        $pagos = $this->repository->registrarPagoLote($command->idSueldo, $command->cuotas);
        foreach ($pagos as $pago) {
            $this->gastos->alPagarDocente($pago->id);
        }

        return $pagos;
    }
}
