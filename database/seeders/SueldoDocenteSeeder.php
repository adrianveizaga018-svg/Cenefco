<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SueldoDocenteSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        
        $sueldos = [
            [
                'id_us'       => 9001,
                'id_imp'      => 1,
                'concepto'    => 'Sueldo Docente — Diplomado en Gestión Pública',
                'periodo'     => '2025-I',
                'gestion'     => 2025,
                'monto_total' => 2500.00,
                'observacion' => 'Pago mensual por 5 meses de dictado',
            ],
            [
                'id_us'       => 9002,
                'id_imp'      => 2,
                'concepto'    => 'Sueldo Docente — Fundamentos de Administración',
                'periodo'     => '2025-I',
                'gestion'     => 2025,
                'monto_total' => 1800.00,
                'observacion' => null,
            ],
            [
                'id_us'       => 9003,
                'id_imp'      => 3,
                'concepto'    => 'Sueldo Docente — Marketing Fundamentos',
                'periodo'     => '2025-I',
                'gestion'     => 2025,
                'monto_total' => 1500.00,
                'observacion' => null,
            ],
            [
                'id_us'       => 9004,
                'id_imp'      => 4,
                'concepto'    => 'Honorario — Derecho Empresarial Básico',
                'periodo'     => '2025-II',
                'gestion'     => 2025,
                'monto_total' => 2200.00,
                'observacion' => 'Incluye material didáctico',
            ],
            [
                'id_us'       => 9005,
                'id_imp'      => 5,
                'concepto'    => 'Sueldo Docente — Marketing Digital',
                'periodo'     => '2026-I',
                'gestion'     => 2026,
                'monto_total' => 1700.00,
                'observacion' => null,
            ],
            [
                'id_us'       => 9001,
                'id_imp'      => null,
                'concepto'    => 'Bono por Evaluaciones y Corrección de Tesis',
                'periodo'     => '2025-II',
                'gestion'     => 2025,
                'monto_total' => 600.00,
                'observacion' => 'Revisión de 12 trabajos de grado',
            ],
            [
                'id_us'       => 9002,
                'id_imp'      => null,
                'concepto'    => 'Coordinación Académica — Diplomado Administración',
                'periodo'     => '2026-I',
                'gestion'     => 2026,
                'monto_total' => 800.00,
                'observacion' => 'Rol de coordinador del módulo',
            ],
        ];

        $ids = [];
        foreach ($sueldos as $s) {
            $id = DB::table('web_sueldo_docente')->insertGetId(array_merge($s, [
                'estado'     => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
            $ids[] = $id;
        }

        
        $pagosData = [
            
            [
                ['id_sueldo' => $ids[0], 'monto_pagado' => 1000.00, 'fecha_pago' => '2025-02-15', 'nro_comprobante' => 'BOL-001'],
                ['id_sueldo' => $ids[0], 'monto_pagado' => 1000.00, 'fecha_pago' => '2025-03-15', 'nro_comprobante' => 'BOL-002'],
                ['id_sueldo' => $ids[0], 'monto_pagado' => 500.00,  'fecha_pago' => '2025-04-15', 'nro_comprobante' => 'BOL-003'],
            ],
            
            [
                ['id_sueldo' => $ids[1], 'monto_pagado' => 900.00, 'fecha_pago' => '2025-02-20', 'nro_comprobante' => 'BOL-004'],
            ],
            
            [],
            
            [
                ['id_sueldo' => $ids[3], 'monto_pagado' => 1100.00, 'fecha_pago' => '2025-07-10', 'nro_comprobante' => 'BOL-005'],
                ['id_sueldo' => $ids[3], 'monto_pagado' => 700.00,  'fecha_pago' => '2025-08-10', 'nro_comprobante' => 'BOL-006'],
            ],
            
            [
                ['id_sueldo' => $ids[4], 'monto_pagado' => 850.00, 'fecha_pago' => '2026-02-01', 'nro_comprobante' => 'BOL-007'],
                ['id_sueldo' => $ids[4], 'monto_pagado' => 850.00, 'fecha_pago' => '2026-03-01', 'nro_comprobante' => 'BOL-008'],
            ],
            
            [
                ['id_sueldo' => $ids[5], 'monto_pagado' => 600.00, 'fecha_pago' => '2025-09-05', 'nro_comprobante' => 'BOL-009', 'observacion' => 'Pago único'],
            ],
            
            [
                ['id_sueldo' => $ids[6], 'monto_pagado' => 800.00, 'fecha_pago' => '2026-04-01', 'nro_comprobante' => 'BOL-011', 'observacion' => 'Pago completo'],
            ],
        ];

        foreach ($pagosData as $gruposPagos) {
            foreach ($gruposPagos as $pago) {
                DB::table('web_pago_sueldo')->insertOrIgnore(array_merge($pago, [
                    'nro_comprobante' => $pago['nro_comprobante'] ?? null,
                    'observacion'     => $pago['observacion'] ?? null,
                    'estado'          => 1,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]));
            }
        }

        $this->command->info('SueldoDocenteSeeder: ' . count($sueldos) . ' sueldos y ' .
            array_sum(array_map('count', $pagosData)) . ' pagos insertados.');
    }
}
