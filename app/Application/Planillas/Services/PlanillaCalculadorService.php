<?php

namespace App\Application\Planillas\Services;

use App\Application\AjustesSueldo\DTOs\AjusteSueldoDTO;
use App\Application\Planillas\DTOs\PlanillaPreviewItemDTO;
use App\Domain\AjustesSueldo\Contracts\AjusteSueldoRepositoryInterface;
use App\Domain\Empleados\Contracts\EmpleadoRepositoryInterface;

class PlanillaCalculadorService
{
    public function __construct(
        private readonly EmpleadoRepositoryInterface $empleadoRepository,
        private readonly AjusteSueldoRepositoryInterface $ajusteRepository,
    ) {}

    public function calcularPreview(int $anio, int $mes): array
    {
        $empleados = $this->empleadoRepository->findAllActivos();

        return array_map(function ($empleado) use ($anio, $mes) {
            $ajustesModelos = $this->ajusteRepository->pendientesDelPeriodo($empleado->id, $anio, $mes);
            $ajustesDto     = array_map(fn ($a) => AjusteSueldoDTO::fromModel($a), $ajustesModelos);

            $totalDescuentos = array_sum(array_map(fn ($a) => $a->tipo === 'descuento' ? $a->monto : 0.0, $ajustesDto));
            $totalBonos      = array_sum(array_map(fn ($a) => $a->tipo === 'bono' ? $a->monto : 0.0, $ajustesDto));
            $montoNeto       = max(0.0, $empleado->sueldo_mensual - $totalDescuentos + $totalBonos);

            return new PlanillaPreviewItemDTO(
                empleado_id:      $empleado->id,
                nombre_completo:  $empleado->nombre_completo,
                cargo:            $empleado->cargo,
                monto_base:       $empleado->sueldo_mensual,
                total_descuentos: $totalDescuentos,
                total_bonos:      $totalBonos,
                monto_neto:       $montoNeto,
                ajustes:          $ajustesDto,
            );
        }, $empleados);
    }
}
