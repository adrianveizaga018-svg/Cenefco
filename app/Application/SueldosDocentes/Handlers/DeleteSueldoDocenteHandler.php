<?php

namespace App\Application\SueldosDocentes\Handlers;

use App\Application\SueldosDocentes\Commands\DeleteSueldoDocenteCommand;
use App\Application\Gastos\Services\GastosAutomaticosService;
use App\Domain\SueldosDocentes\Contracts\SueldoDocenteRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DeleteSueldoDocenteHandler
{
    public function __construct(
        private readonly SueldoDocenteRepositoryInterface $repository,
        private readonly GastosAutomaticosService $gastos,
    ) {}

    public function handle(DeleteSueldoDocenteCommand $command): void
    {
        $this->repository->delete($command->id);
        // Un sueldo dado de baja no debe seguir sumando en los gastos
        foreach (DB::table('web_pago_sueldo')->where('id_sueldo', $command->id)->pluck('id') as $idPago) {
            $this->gastos->alAnularPagoDocente((int) $idPago);
        }
    }
}
