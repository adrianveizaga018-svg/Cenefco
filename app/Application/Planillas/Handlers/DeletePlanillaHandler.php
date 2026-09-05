<?php

namespace App\Application\Planillas\Handlers;

use App\Application\Planillas\Commands\DeletePlanillaCommand;
use App\Domain\AjustesSueldo\Contracts\AjusteSueldoRepositoryInterface;
use App\Domain\Gastos\Contracts\GastoRepositoryInterface;
use App\Domain\Planillas\Contracts\PlanillaRepositoryInterface;
use App\Domain\Planillas\Exceptions\PlanillaNoEliminableException;
use Illuminate\Support\Facades\DB;

class DeletePlanillaHandler
{
    public function __construct(
        private readonly PlanillaRepositoryInterface    $planillaRepository,
        private readonly GastoRepositoryInterface        $gastoRepository,
        private readonly AjusteSueldoRepositoryInterface $ajusteRepository,
    ) {}

    public function handle(DeletePlanillaCommand $c): bool
    {
        $planilla = $this->planillaRepository->findById($c->id);

        $ahora = now();
        if ($planilla->anio !== (int) $ahora->year || $planilla->mes !== (int) $ahora->month) {
            throw new PlanillaNoEliminableException();
        }

        return DB::transaction(function () use ($c, $planilla) {
            $detalleIds = array_map(fn ($d) => $d->id, $planilla->detalle);
            $this->ajusteRepository->desaplicarPorPlanillaDetalle($detalleIds);

            $this->planillaRepository->delete($c->id);

            return $this->gastoRepository->delete($planilla->gasto_id);
        });
    }
}
