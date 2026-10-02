<?php

namespace Database\Seeders;

use App\Infrastructure\Empleados\Models\Empleado;
use App\Infrastructure\Gastos\Models\CategoriaGasto;
use App\Infrastructure\Gastos\Models\Gasto;
use App\Infrastructure\Planillas\Models\Planilla;
use App\Infrastructure\Planillas\Models\PlanillaDetalle;
use Illuminate\Database\Seeder;

class PlanillaSeeder extends Seeder
{
    public function run(): void
    {
        $empleados = Empleado::where('activo', true)->get();
        if ($empleados->isEmpty()) {
            return;
        }

        $categoriaSueldosId = CategoriaGasto::where('nombre', 'Sueldos')->value('id');
        if (! $categoriaSueldosId) {
            return;
        }

        $total = (float) $empleados->sum('sueldo_mensual');

        foreach ([['anio' => 2026, 'mes' => 4], ['anio' => 2026, 'mes' => 5]] as $periodo) {
            if (Planilla::where('anio', $periodo['anio'])->where('mes', $periodo['mes'])->exists()) {
                continue;
            }

            $fecha = sprintf('%04d-%02d-30', $periodo['anio'], $periodo['mes']);

            $gasto = Gasto::create([
                'categoria_gasto_id' => $categoriaSueldosId,
                'concepto'           => sprintf('Planilla de sueldos %02d/%04d', $periodo['mes'], $periodo['anio']),
                'monto'              => $total,
                'fecha'              => $fecha,
                'responsable'        => 'Administración',
            ]);

            $planilla = Planilla::create([
                'anio'     => $periodo['anio'],
                'mes'      => $periodo['mes'],
                'total'    => $total,
                'gasto_id' => $gasto->id,
            ]);

            foreach ($empleados as $emp) {
                PlanillaDetalle::create([
                    'planilla_id'     => $planilla->id,
                    'empleado_id'     => $emp->id,
                    'nombre_completo' => $emp->nombre_completo,
                    'cargo'           => $emp->cargo,
                    'monto'           => $emp->sueldo_mensual,
                ]);
            }
        }
    }
}
