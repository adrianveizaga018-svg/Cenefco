<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImparticionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        $imparticiones = [
            [
                'id_imp'    => 1,
                'num_imp'   => 1,
                'periodo'   => '2025-I',
                'gestion'   => '2025',
                'id_us'     => 9001,
                'id_mat'    => 9001,
                'paralelo'  => 'A',
                'cupo'      => '30',
                'observacion_imp' => 'Grupo regular presencial',
                'nro_resolucion_hcu' => 'HCU-001/2025',
            ],
            [
                'id_imp'    => 2,
                'num_imp'   => 2,
                'periodo'   => '2025-I',
                'gestion'   => '2025',
                'id_us'     => 9002,
                'id_mat'    => 9002,
                'paralelo'  => 'A',
                'cupo'      => '25',
                'observacion_imp' => null,
                'nro_resolucion_hcu' => 'HCU-002/2025',
            ],
            [
                'id_imp'    => 3,
                'num_imp'   => 3,
                'periodo'   => '2025-I',
                'gestion'   => '2025',
                'id_us'     => 9003,
                'id_mat'    => 9004,
                'paralelo'  => 'A',
                'cupo'      => '20',
                'observacion_imp' => null,
                'nro_resolucion_hcu' => 'HCU-003/2025',
            ],
            [
                'id_imp'    => 4,
                'num_imp'   => 4,
                'periodo'   => '2025-II',
                'gestion'   => '2025',
                'id_us'     => 9004,
                'id_mat'    => 9007,
                'paralelo'  => 'Único',
                'cupo'      => '35',
                'observacion_imp' => 'Modalidad semipresencial',
                'nro_resolucion_hcu' => 'HCU-004/2025',
            ],
            [
                'id_imp'    => 5,
                'num_imp'   => 5,
                'periodo'   => '2026-I',
                'gestion'   => '2026',
                'id_us'     => 9005,
                'id_mat'    => 9008,
                'paralelo'  => 'A',
                'cupo'      => '25',
                'observacion_imp' => 'Grupo online — Zoom',
                'nro_resolucion_hcu' => 'HCU-001/2026',
            ],
            [
                'id_imp'    => 6,
                'num_imp'   => 6,
                'periodo'   => '2026-I',
                'gestion'   => '2026',
                'id_us'     => 9001,
                'id_mat'    => 9009,
                'paralelo'  => 'Único',
                'cupo'      => '20',
                'observacion_imp' => 'Certificación intensiva',
                'nro_resolucion_hcu' => 'HCU-002/2026',
            ],
        ];

        foreach ($imparticiones as $i) {
            if (DB::table('t_imparte')->where('id_imp', $i['id_imp'])->where('id_us_reg', 1)->exists()) continue;
            DB::table('t_imparte')->insertOrIgnore(array_merge($i, [
                'id_us_reg' => 1,
                'estado'    => 1,
                'fecha_reg' => $now,
            ]));
        }

        $this->command->info('✓ ' . count($imparticiones) . ' imparticiones insertadas en t_imparte.');
    }
}
