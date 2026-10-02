<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaPlanSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        $materias = [
            ['id_mat' => 9001, 'sigla' => 'DEMO-2026',  'nombre' => 'Diplomado en Gestión Pública',        'carga_horaria' => 240, 'semestre' => 1],
            ['id_mat' => 9002, 'sigla' => 'ADM-101',    'nombre' => 'Fundamentos de Administración',       'carga_horaria' => 80,  'semestre' => 1],
            ['id_mat' => 9003, 'sigla' => 'CONT-101',   'nombre' => 'Contabilidad Básica',                 'carga_horaria' => 80,  'semestre' => 1],
            ['id_mat' => 9004, 'sigla' => 'MKT-101',    'nombre' => 'Marketing Fundamentos',               'carga_horaria' => 80,  'semestre' => 2],
            ['id_mat' => 9005, 'sigla' => 'FIN-101',    'nombre' => 'Finanzas Empresariales',              'carga_horaria' => 80,  'semestre' => 2],
            ['id_mat' => 9006, 'sigla' => 'GEST-201',   'nombre' => 'Gestión de Proyectos',                'carga_horaria' => 100, 'semestre' => 3],
            ['id_mat' => 9007, 'sigla' => 'DER-101',    'nombre' => 'Derecho Empresarial Básico',          'carga_horaria' => 80,  'semestre' => 1],
            ['id_mat' => 9008, 'sigla' => 'MKT-DIG-101','nombre' => 'Marketing Digital',                   'carga_horaria' => 100, 'semestre' => 2],
            ['id_mat' => 9009, 'sigla' => 'PMI-101',    'nombre' => 'Fundamentos PMI — PMBOK',             'carga_horaria' => 60,  'semestre' => 1],
            ['id_mat' => 9010, 'sigla' => 'EST-101',    'nombre' => 'Estadística para Negocios',           'carga_horaria' => 80,  'semestre' => 2],
        ];

        foreach ($materias as $m) {
            if (DB::table('t_materia')->where('id_mat', $m['id_mat'])->exists()) continue;
            DB::table('t_materia')->insertOrIgnore([
                'id_mat'        => $m['id_mat'],
                'id_us_reg'     => 1,
                'num_mat'       => $m['id_mat'] - 9000,
                'sigla'         => $m['sigla'],
                'nombremat'     => $m['nombre'],
                'nombre'        => $m['nombre'],
                'semestre'      => $m['semestre'],
                'carga_horaria' => $m['carga_horaria'],
                'modalidad'     => 0,
                'observacion'   => '',
                'estado'        => 1,
                'fecha_reg'     => $now,
            ]);
        }

        $relaciones = [
            ['id_matplan' => 1001, 'id_mat' => 9001, 'id_plan' => 101, 'carga_horaria_plan' => '240 horas', 'id_preesp' => 1],
            ['id_matplan' => 1002, 'id_mat' => 9002, 'id_plan' => 101, 'carga_horaria_plan' => '80 horas',  'id_preesp' => 1],
            ['id_matplan' => 1003, 'id_mat' => 9003, 'id_plan' => 101, 'carga_horaria_plan' => '80 horas',  'id_preesp' => 1],
            ['id_matplan' => 1004, 'id_mat' => 9010, 'id_plan' => 101, 'carga_horaria_plan' => '80 horas',  'id_preesp' => 1],

            ['id_matplan' => 1005, 'id_mat' => 9002, 'id_plan' => 102, 'carga_horaria_plan' => '80 horas',  'id_preesp' => 1],
            ['id_matplan' => 1006, 'id_mat' => 9003, 'id_plan' => 102, 'carga_horaria_plan' => '80 horas',  'id_preesp' => 1],
            ['id_matplan' => 1007, 'id_mat' => 9004, 'id_plan' => 102, 'carga_horaria_plan' => '80 horas',  'id_preesp' => 1],
            ['id_matplan' => 1008, 'id_mat' => 9005, 'id_plan' => 102, 'carga_horaria_plan' => '80 horas',  'id_preesp' => 1],
            ['id_matplan' => 1009, 'id_mat' => 9006, 'id_plan' => 102, 'carga_horaria_plan' => '100 horas', 'id_preesp' => 1],
            ['id_matplan' => 1010, 'id_mat' => 9010, 'id_plan' => 102, 'carga_horaria_plan' => '80 horas',  'id_preesp' => 1],

            ['id_matplan' => 1011, 'id_mat' => 9007, 'id_plan' => 103, 'carga_horaria_plan' => '80 horas',  'id_preesp' => 1],
            ['id_matplan' => 1012, 'id_mat' => 9002, 'id_plan' => 103, 'carga_horaria_plan' => '80 horas',  'id_preesp' => 1],

            ['id_matplan' => 1013, 'id_mat' => 9004, 'id_plan' => 104, 'carga_horaria_plan' => '80 horas',  'id_preesp' => 1],
            ['id_matplan' => 1014, 'id_mat' => 9008, 'id_plan' => 104, 'carga_horaria_plan' => '100 horas', 'id_preesp' => 1],

            ['id_matplan' => 1015, 'id_mat' => 9009, 'id_plan' => 105, 'carga_horaria_plan' => '60 horas',  'id_preesp' => 1],
            ['id_matplan' => 1016, 'id_mat' => 9006, 'id_plan' => 105, 'carga_horaria_plan' => '100 horas', 'id_preesp' => 1],
        ];

        foreach ($relaciones as $r) {
            if (DB::table('t_materia_plan')->where('id_matplan', $r['id_matplan'])->exists()) continue;
            DB::table('t_materia_plan')->insertOrIgnore(array_merge($r, [
                'id_us_reg' => 1,
                'num_mp'    => $r['id_matplan'] - 1000,
                'estado'    => 1,
                'fecha_reg' => $now,
            ]));
        }

        $this->command->info('✓ ' . count($materias) . ' materias insertadas en t_materia.');
        $this->command->info('✓ ' . count($relaciones) . ' relaciones insertadas en t_materia_plan.');
    }
}
