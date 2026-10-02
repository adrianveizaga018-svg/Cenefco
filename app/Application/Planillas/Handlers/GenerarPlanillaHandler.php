<?php

namespace App\Application\Planillas\Handlers;

use App\Application\Gastos\Commands\CreateGastoCommand;
use App\Application\Gastos\Handlers\CreateGastoHandler;
use App\Application\Planillas\Commands\GenerarPlanillaCommand;
use App\Application\Planillas\DTOs\PlanillaDTO;
use App\Application\Planillas\Services\PlanillaCalculadorService;
use App\Domain\AjustesSueldo\Contracts\AjusteSueldoRepositoryInterface;
use App\Domain\Planillas\Contracts\PlanillaRepositoryInterface;
use App\Domain\Planillas\Exceptions\PlanillaDuplicadaException;
use Illuminate\Support\Facades\DB;

class GenerarPlanillaHandler
{
    private const CATEGORIA_SUELDOS_NOMBRE = 'Sueldos';

    public function __construct(
        private readonly PlanillaRepositoryInterface      $planillaRepository,
        private readonly PlanillaCalculadorService         $calculador,
        private readonly AjusteSueldoRepositoryInterface   $ajusteRepository,
        private readonly CreateGastoHandler                $createGastoHandler,
    ) {}

    public function handle(GenerarPlanillaCommand $c): PlanillaDTO
    {
        if ($this->planillaRepository->existePlanillaDelMes($c->anio, $c->mes)) {
            throw new PlanillaDuplicadaException($c->anio, $c->mes);
        }

        $preview = $this->calculador->calcularPreview($c->anio, $c->mes);
        $total   = array_sum(array_map(fn ($item) => $item->monto_neto, $preview));

        return DB::transaction(function () use ($c, $preview, $total) {
            $gasto = $this->createGastoHandler->handle(new CreateGastoCommand(
                categoria_gasto_id: $this->buscarOCrearCategoriaSueldos(),
                concepto:           "Planilla de sueldos {$c->mes}/{$c->anio}",
                monto:              $total,
                fecha:              now()->toDateString(),
            ));

            $detalle = array_map(fn ($item) => [
                'empleado_id'      => $item->empleado_id,
                'nombre_completo'  => $item->nombre_completo,
                'cargo'            => $item->cargo,
                'monto'            => $item->monto_neto,
                'monto_base'       => $item->monto_base,
                'total_descuentos' => $item->total_descuentos,
                'total_bonos'      => $item->total_bonos,
            ], $preview);

            $planilla = $this->planillaRepository->create([
                'anio'     => $c->anio,
                'mes'      => $c->mes,
                'total'    => $total,
                'gasto_id' => $gasto->id,
            ], $detalle);

            $detalleIdPorEmpleado = [];
            foreach ($planilla->detalle as $d) {
                $detalleIdPorEmpleado[$d->empleado_id] = $d->id;
            }

            foreach ($preview as $item) {
                if (empty($item->ajustes)) {
                    continue;
                }
                $planillaDetalleId = $detalleIdPorEmpleado[$item->empleado_id] ?? null;
                if ($planillaDetalleId) {
                    $this->ajusteRepository->marcarAplicados(
                        array_map(fn ($a) => $a->id, $item->ajustes),
                        $planillaDetalleId,
                    );
                }
            }

            return $planilla;
        });
    }

    private function buscarOCrearCategoriaSueldos(): int
    {
        return DB::table('categoria_gasto')
            ->where('nombre', self::CATEGORIA_SUELDOS_NOMBRE)
            ->value('id')
            ?? DB::table('categoria_gasto')->insertGetId([
                'nombre'     => self::CATEGORIA_SUELDOS_NOMBRE,
                'activo'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    }
}
