<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanAcademicoSeeder extends Seeder
{
    public function run(): void
    {
        $planes = [
            [
                'id_plan'           => 101,
                'titulo'            => 'Plan de Estudios — Diplomado en Gestión Pública 2025',
                'titulo_plan'       => 'DIPL-GESTION-PUB-2025',
                'convenio'          => 'Universidad San Francisco de Asís (USFA)',
                'anio'              => '2025',
                'numero_resolucion' => 'R-001/2025',
                'costo'             => '3500',
                'nro_cuotas'        => '6',
                'descuento'         => '10',
                'costo_por_cuota'   => '583',
                'id_catplan'        => null,
            ],
            [
                'id_plan'           => 102,
                'titulo'            => 'Plan de Estudios — Maestría en Administración de Empresas (MBA)',
                'titulo_plan'       => 'MBA-2025',
                'convenio'          => 'Universidad San Francisco de Asís (USFA)',
                'anio'              => '2025',
                'numero_resolucion' => 'R-002/2025',
                'costo'             => '8500',
                'nro_cuotas'        => '12',
                'descuento'         => '5',
                'costo_por_cuota'   => '708',
                'id_catplan'        => null,
            ],
            [
                'id_plan'           => 103,
                'titulo'            => 'Plan de Estudios — Diplomado en Derecho Empresarial',
                'titulo_plan'       => 'DIPL-DER-EMP-2025',
                'convenio'          => 'Universidad San Francisco de Asís (USFA)',
                'anio'              => '2025',
                'numero_resolucion' => 'R-003/2025',
                'costo'             => '2800',
                'nro_cuotas'        => '5',
                'descuento'         => '0',
                'costo_por_cuota'   => '560',
                'id_catplan'        => null,
            ],
            [
                'id_plan'           => 104,
                'titulo'            => 'Plan de Estudios — Especialización en Marketing Digital',
                'titulo_plan'       => 'ESP-MKT-DIG-2026',
                'convenio'          => 'Universidad San Francisco de Asís (USFA)',
                'anio'              => '2026',
                'numero_resolucion' => 'R-001/2026',
                'costo'             => '4200',
                'nro_cuotas'        => '6',
                'descuento'         => '8',
                'costo_por_cuota'   => '700',
                'id_catplan'        => null,
            ],
            [
                'id_plan'           => 105,
                'titulo'            => 'Plan de Estudios — Certificación en Gestión de Proyectos PMI',
                'titulo_plan'       => 'CERT-PMI-2026',
                'convenio'          => 'Universidad San Francisco de Asís (USFA)',
                'anio'              => '2026',
                'numero_resolucion' => 'R-002/2026',
                'costo'             => '1800',
                'nro_cuotas'        => '3',
                'descuento'         => '0',
                'costo_por_cuota'   => '600',
                'id_catplan'        => null,
            ],
        ];

        $now = now()->toDateTimeString();
        foreach ($planes as $p) {
            if (DB::table('t_plan')->where('id_plan', $p['id_plan'])->exists()) continue;
            DB::table('t_plan')->insertOrIgnore(array_merge($p, [
                'id_us_reg' => 1,
                'num_plan'  => $p['id_plan'],
                'estado'    => 1,
                'fecha_reg' => $now,
            ]));
        }

        $this->command->info('✓ ' . count($planes) . ' planes académicos insertados en t_plan.');
    }
}
