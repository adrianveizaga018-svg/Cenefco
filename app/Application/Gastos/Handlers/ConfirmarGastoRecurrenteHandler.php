<?php

namespace App\Application\Gastos\Handlers;

use App\Application\Gastos\Commands\ConfirmarGastoRecurrenteCommand;
use App\Application\Gastos\DTOs\GastoDTO;
use App\Domain\Gastos\Contracts\GastoRecurrenteRepositoryInterface;
use App\Domain\Gastos\Contracts\GastoRepositoryInterface;
use App\Domain\Gastos\Exceptions\GastoRecurrenteYaConfirmadoException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ConfirmarGastoRecurrenteHandler
{
    public function __construct(
        private readonly GastoRecurrenteRepositoryInterface $recurrenteRepository,
        private readonly GastoRepositoryInterface           $gastoRepository,
    ) {}

    public function handle(ConfirmarGastoRecurrenteCommand $c): GastoDTO
    {
        return DB::transaction(function () use ($c) {
            $recurrente = $this->recurrenteRepository->findById($c->gasto_recurrente_id);

            if ($recurrente->ultima_confirmacion) {
                $ultima = Carbon::parse($recurrente->ultima_confirmacion);
                $fecha  = Carbon::parse($c->fecha);
                if ($ultima->year === $fecha->year && $ultima->month === $fecha->month) {
                    throw new GastoRecurrenteYaConfirmadoException();
                }
            }

            $gasto = $this->gastoRepository->create([
                'categoria_gasto_id'  => $recurrente->categoria_gasto_id,
                'concepto'            => $recurrente->concepto,
                'monto'               => $recurrente->monto,
                'fecha'               => $c->fecha,
                'gasto_recurrente_id' => $recurrente->id,
                'comprobante_url'     => $c->comprobante_url,
            ]);

            $this->recurrenteRepository->marcarConfirmado($recurrente->id, $c->fecha);

            return $gasto;
        });
    }
}
