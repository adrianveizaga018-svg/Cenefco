<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FechaDocSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        $fechasDocs = [
            [
                'id_fechadoc'   => 301,
                'id_plandoc'    => 101,
                'num_fechadoc'  => 1,
                'nro_doc'       => '1',
                'tipo_documento'=> 'Fotocopia de CI',
                'fecha_inicio'  => '2025-02-01',
                'fecha_fin'     => '2025-02-28',
                'obligatorio'   => 1,
            ],
            [
                'id_fechadoc'   => 302,
                'id_plandoc'    => 101,
                'num_fechadoc'  => 2,
                'nro_doc'       => '2',
                'tipo_documento'=> 'Título o Grado Académico',
                'fecha_inicio'  => '2025-02-01',
                'fecha_fin'     => '2025-02-28',
                'obligatorio'   => 1,
            ],
            [
                'id_fechadoc'   => 303,
                'id_plandoc'    => 101,
                'num_fechadoc'  => 3,
                'nro_doc'       => '3',
                'tipo_documento'=> 'Fotografía 3x3',
                'fecha_inicio'  => '2025-02-01',
                'fecha_fin'     => '2025-03-15',
                'obligatorio'   => 1,
            ],
            [
                'id_fechadoc'   => 304,
                'id_plandoc'    => 101,
                'num_fechadoc'  => 4,
                'nro_doc'       => '4',
                'tipo_documento'=> 'Currículum Vitae',
                'fecha_inicio'  => '2025-02-01',
                'fecha_fin'     => '2025-03-15',
                'obligatorio'   => 0,
            ],
            [
                'id_fechadoc'   => 305,
                'id_plandoc'    => 102,
                'num_fechadoc'  => 1,
                'nro_doc'       => '1',
                'tipo_documento'=> 'Fotocopia de CI',
                'fecha_inicio'  => '2025-02-01',
                'fecha_fin'     => '2025-02-28',
                'obligatorio'   => 1,
            ],
            [
                'id_fechadoc'   => 306,
                'id_plandoc'    => 102,
                'num_fechadoc'  => 2,
                'nro_doc'       => '2',
                'tipo_documento'=> 'Título Profesional',
                'fecha_inicio'  => '2025-02-01',
                'fecha_fin'     => '2025-02-28',
                'obligatorio'   => 1,
            ],
            [
                'id_fechadoc'   => 307,
                'id_plandoc'    => 102,
                'num_fechadoc'  => 3,
                'nro_doc'       => '3',
                'tipo_documento'=> 'Carta de Recomendación',
                'fecha_inicio'  => '2025-02-01',
                'fecha_fin'     => '2025-03-31',
                'obligatorio'   => 0,
            ],
            [
                'id_fechadoc'   => 308,
                'id_plandoc'    => 104,
                'num_fechadoc'  => 1,
                'nro_doc'       => '1',
                'tipo_documento'=> 'Fotocopia de CI',
                'fecha_inicio'  => '2026-01-15',
                'fecha_fin'     => '2026-02-15',
                'obligatorio'   => 1,
            ],
            [
                'id_fechadoc'   => 309,
                'id_plandoc'    => 104,
                'num_fechadoc'  => 2,
                'nro_doc'       => '2',
                'tipo_documento'=> 'Certificado de Trabajo o Estudio',
                'fecha_inicio'  => '2026-01-15',
                'fecha_fin'     => '2026-02-28',
                'obligatorio'   => 0,
            ],
        ];

        foreach ($fechasDocs as $f) {
            $existe = DB::table('t_fechadoc')
                ->where('id_fechadoc', $f['id_fechadoc'])
                ->where('id_plandoc', $f['id_plandoc'])
                ->where('id_us_reg', 1)
                ->exists();
            if ($existe) continue;
            DB::table('t_fechadoc')->insertOrIgnore(array_merge($f, [
                'id_us_reg' => 1,
                'estado'    => 1,
                'fecha_reg' => $now,
            ]));
        }

        $this->command->info('✓ ' . count($fechasDocs) . ' fechas de documentación insertadas en t_fechadoc.');
    }
}
