<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequisitoAcademicoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        $requisitos = [
            [
                'id_req'       => 1,
                'id_mat'       => 9005,
                'pre_id_mat'   => 9003,
                'todos1a6'     => 'No',
                'modalidad_req'=> 'obligatorio',
            ],
            [
                'id_req'       => 2,
                'id_mat'       => 9008,
                'pre_id_mat'   => 9004,
                'todos1a6'     => 'No',
                'modalidad_req'=> 'obligatorio',
            ],
            [
                'id_req'       => 3,
                'id_mat'       => 9006,
                'pre_id_mat'   => 9002,
                'todos1a6'     => 'No',
                'modalidad_req'=> 'recomendado',
            ],
            [
                'id_req'       => 4,
                'id_mat'       => 9010,
                'pre_id_mat'   => 9002,
                'todos1a6'     => 'No',
                'modalidad_req'=> 'recomendado',
            ],
            [
                'id_req'       => 5,
                'id_mat'       => 9009,
                'pre_id_mat'   => 9006,
                'todos1a6'     => 'No',
                'modalidad_req'=> 'obligatorio',
            ],
        ];

        foreach ($requisitos as $r) {
            $existe = DB::table('t_requisito')
                ->where('id_req', $r['id_req'])
                ->where('id_mat', $r['id_mat'])
                ->where('id_us_reg', 1)
                ->exists();
            if ($existe) continue;
            DB::table('t_requisito')->insertOrIgnore(array_merge($r, [
                'id_us_reg' => 1,
                'num_req'   => $r['id_req'],
                'estado'    => 1,
                'fecha_reg' => $now,
            ]));
        }

        $this->command->info('✓ ' . count($requisitos) . ' requisitos académicos insertados en t_requisito.');
    }
}
