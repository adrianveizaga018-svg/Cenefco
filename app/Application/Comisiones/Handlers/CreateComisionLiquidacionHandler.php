<?php

namespace App\Application\Comisiones\Handlers;

use App\Application\Comisiones\Commands\CreateComisionLiquidacionCommand;
use App\Application\Comisiones\DTOs\ComisionLiquidacionDetalleDTO;
use App\Application\Comisiones\DTOs\ComisionLiquidacionDTO;
use App\Application\Comisiones\Services\ComisionCalculadorService;
use App\Domain\Comisiones\Contracts\ComisionLiquidacionRepositoryInterface;
use App\Domain\Comisiones\Exceptions\SinPagosElegiblesException;
use App\Domain\Vendedores\Contracts\VendedorRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreateComisionLiquidacionHandler
{
    public function __construct(
        private readonly ComisionLiquidacionRepositoryInterface $repository,
        private readonly VendedorRepositoryInterface             $vendedorRepository,
        private readonly ComisionCalculadorService                $calculador,
    ) {}

    public function handle(CreateComisionLiquidacionCommand $command): ComisionLiquidacionDTO
    {
        $this->vendedorRepository->findById($command->vendedorId);

        $calculo = $this->calculador->calcular(
            $command->vendedorId,
            $command->fechaDesde,
            $command->fechaHasta,
        );

        if (empty($calculo['inscritos'])) {
            throw new SinPagosElegiblesException();
        }

        return DB::transaction(function () use ($command, $calculo) {
            $model = $this->repository->create(
                [
                    'vendedor_id'     => $command->vendedorId,
                    'fecha_desde'     => $command->fechaDesde,
                    'fecha_hasta'     => $command->fechaHasta,
                    'total_inscritos' => $calculo['total_inscritos'],
                    'monto_comision'  => $calculo['monto_comision'],
                    'estado'          => 'calculado',
                    'nota'            => $command->nota,
                ],
                array_map(fn ($i) => [
                    'id_pago'        => $i->id_pago,
                    'id_ins'         => $i->id_ins,
                    'categoria_id'   => $i->categoria_id,
                    'comision_monto' => $i->comision_monto,
                    'fecha_deposito' => $i->fecha_deposito,
                ], $calculo['inscritos'])
            );

            $detalle = collect($model->detalle ?? [])
                ->map(fn ($d) => ComisionLiquidacionDetalleDTO::fromModel($d))
                ->all();

            return ComisionLiquidacionDTO::fromModel($model, $detalle);
        });
    }
}
