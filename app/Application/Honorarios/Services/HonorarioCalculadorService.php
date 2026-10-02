<?php

namespace App\Application\Honorarios\Services;

use App\Domain\Honorarios\Enums\TipoHonorario;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class HonorarioCalculadorService
{
    public function calcular(
        string $tipoHonorario,
        ?float $montoFijo,
        ?float $montoPorDia,
        array  $diasSemana,
        string $fechaInicio,
        string $fechaFin,
    ): array {
        return match ($tipoHonorario) {
            TipoHonorario::DiplomadoFijo->value => $this->calcularDiplomadoFijo($montoFijo),
            TipoHonorario::RmPorDia->value,
            TipoHonorario::AvalPorDia->value    => $this->calcularPorDia($montoPorDia, $diasSemana, $fechaInicio, $fechaFin),
            default                              => ['dias_dictados' => 0, 'monto_sugerido' => 0.0],
        };
    }

    private function calcularDiplomadoFijo(?float $montoFijo): array
    {
        return [
            'dias_dictados'  => 0,
            'monto_sugerido' => $montoFijo ?? 0.0,
        ];
    }

    private function calcularPorDia(?float $montoPorDia, array $diasSemana, string $fechaInicio, string $fechaFin): array
    {
        if (! $montoPorDia || empty($diasSemana)) {
            return ['dias_dictados' => 0, 'monto_sugerido' => 0.0];
        }

        $inicio = Carbon::parse($fechaInicio);
        $fin    = Carbon::parse($fechaFin);

        if ($fin->lessThan($inicio)) {
            return ['dias_dictados' => 0, 'monto_sugerido' => 0.0];
        }

        $diasSemanaInt = array_map('intval', $diasSemana);

        $diasDictados = 0;
        foreach (CarbonPeriod::create($inicio, $fin) as $fecha) {
            if (in_array($fecha->dayOfWeek, $diasSemanaInt, true)) {
                $diasDictados++;
            }
        }

        return [
            'dias_dictados'  => $diasDictados,
            'monto_sugerido' => round($diasDictados * $montoPorDia, 2),
        ];
    }
}
