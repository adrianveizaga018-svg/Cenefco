<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupoAcademicoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        $grupos = [
            [
                'id_grupo'      => 1,
                'num_grupo'     => 1,
                'siglagrupo'    => 'GES-PUB-A',
                'nombregrupo'   => 'Diplomado Gestión Pública — Grupo A',
                'titulo'        => 'Grupo Regular — 2025-I',
                'espacio_laboral' => 'Aula 301 — USFA La Paz',
                'id_test'       => null,
            ],
            [
                'id_grupo'      => 2,
                'num_grupo'     => 2,
                'siglagrupo'    => 'MBA-A',
                'nombregrupo'   => 'MBA — Grupo A',
                'titulo'        => 'Grupo Regular — 2025-I',
                'espacio_laboral' => 'Aula 205 — USFA La Paz',
                'id_test'       => null,
            ],
            [
                'id_grupo'      => 3,
                'num_grupo'     => 3,
                'siglagrupo'    => 'DER-EMP-A',
                'nombregrupo'   => 'Diplomado Derecho Empresarial — Grupo A',
                'titulo'        => 'Grupo Regular — 2025-II',
                'espacio_laboral' => 'Aula 102 — USFA La Paz',
                'id_test'       => null,
            ],
            [
                'id_grupo'      => 4,
                'num_grupo'     => 4,
                'siglagrupo'    => 'MKT-DIG-A',
                'nombregrupo'   => 'Especialización Marketing Digital — Grupo A',
                'titulo'        => 'Grupo Regular — 2026-I',
                'espacio_laboral' => 'Aula Virtual Zoom',
                'id_test'       => null,
            ],
            [
                'id_grupo'      => 5,
                'num_grupo'     => 5,
                'siglagrupo'    => 'PMI-A',
                'nombregrupo'   => 'Certificación PMI — Grupo A',
                'titulo'        => 'Grupo Intensivo — 2026-I',
                'espacio_laboral' => 'Sala Conferencias — USFA La Paz',
                'id_test'       => null,
            ],
        ];

        foreach ($grupos as $g) {
            if (DB::table('t_grupo')->where('id_grupo', $g['id_grupo'])->where('id_us_reg', 1)->exists()) continue;
            DB::table('t_grupo')->insertOrIgnore(array_merge($g, [
                'id_us_reg' => 1,
                'estado'    => 1,
                'fecha_reg' => $now,
            ]));
        }

        $this->command->info('✓ ' . count($grupos) . ' grupos académicos insertados en t_grupo.');
    }
}
