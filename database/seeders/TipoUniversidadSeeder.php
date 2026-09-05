<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoUniversidadSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [1, 'Universidad Pública Autónoma'],
            [2, 'Universidad Privada'],
            [3, 'Universidad de Régimen Especial'],
            [4, 'Instituto Técnico Superior'],
            [5, 'Escuela Militar / Policial'],
            [6, 'Extranjera'],
            [7, 'Otra'],
        ];

        $now = now();

        foreach ($tipos as [$id, $nombre]) {
            DB::table('t_tipouniversidad')->insertOrIgnore([
                'id_tipouniversidad'    => $id,
                'id_us_reg'             => 1,
                'num_tipouniversidad'   => $id,
                'nombre_tipouniversidad'=> $nombre,
                'fecha_reg'             => $now,
                'estado'                => 1,
                'per_modificar'         => 0,
            ]);
        }

        $total = DB::table('t_tipouniversidad')->count();
        $this->command->info("✓ {$total} tipos de universidad en t_tipouniversidad.");
    }
}
