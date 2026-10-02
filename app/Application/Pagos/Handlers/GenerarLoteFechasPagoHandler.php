<?php

namespace App\Application\Pagos\Handlers;

use App\Application\Pagos\Commands\GenerarLoteFechasPagoCommand;
use App\Application\Pagos\DTOs\FechaPagoDTO;
use App\Domain\Pagos\Contracts\FechaPagoRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerarLoteFechasPagoHandler
{
    public function __construct(
        private readonly FechaPagoRepositoryInterface $repository,
    ) {}

    public function handle(GenerarLoteFechasPagoCommand $command): array
    {
        $montoCuota = round($command->montoTotal / $command->nroCuotas, 2);

        $montoUltimaCuota = round($command->montoTotal - ($montoCuota * ($command->nroCuotas - 1)), 2);
        $fecha      = Carbon::parse($command->fechaInicio);
        $creados    = [];

        return DB::transaction(function () use ($command, $montoCuota, $montoUltimaCuota, $fecha, &$creados) {
            for ($i = 1; $i <= $command->nroCuotas; $i++) {
                $fechaFin = match ($command->intervalo) {
                    'semanal'    => (clone $fecha)->addDays(6),
                    'quincenal'  => (clone $fecha)->addDays(14),
                    default      => (clone $fecha)->addMonth()->subDay(),
                };

                $model = $this->repository->create([
                    'id_plan'       => $command->idPlan,
                    'nro_pago'      => (string) $i,
                    'monto_a_pagar' => $i === $command->nroCuotas ? $montoUltimaCuota : $montoCuota,
                    'fecha_inicio'  => $fecha->toDateString(),
                    'fecha_fin'     => $fechaFin->toDateString(),
                    'obligatorio'   => 1,
                    'tipo_tramite'  => $command->tipoTramite,
                    'id_us_reg'     => $command->idUsReg,
                    'estado'        => 1,
                    'fecha_reg'     => now(),
                ]);

                $creados[] = FechaPagoDTO::fromModel($model);

                $fecha = match ($command->intervalo) {
                    'semanal'   => $fecha->addWeek(),
                    'quincenal' => $fecha->addDays(15),
                    default     => $fecha->addMonth(),
                };
            }

            return $creados;
        });
    }
}
