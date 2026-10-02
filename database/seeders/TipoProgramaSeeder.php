<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoProgramaSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [1, 'Diplomado'],
            [2, 'Maestría'],
            [3, 'Especialización'],
            [4, 'Curso'],
            [5, 'Taller'],
            [6, 'Seminario'],
            [7, 'Certificación'],
            [8, 'Congreso'],
        ];

        $now = now();

        foreach ($tipos as [$id, $nombre]) {
            DB::table('t_tipoprograma')->insertOrIgnore([
                'id_tipoprograma'    => $id,
                'id_us_reg'          => 1,
                'num_tipoprograma'   => $id,
                'nombre_tipoprograma'=> $nombre,
                'fecha_reg'          => $now,
                'estado'             => 1,
                'per_modificar'      => 0,
            ]);
        }

        $total = DB::table('t_tipoprograma')->count();
        $this->command->info("✓ {$total} tipos de programa en t_tipoprograma.");
    }
}
