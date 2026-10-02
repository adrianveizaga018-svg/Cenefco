<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CiudadSeeder extends Seeder
{
    public function run(): void
    {
        $ciudades = [
            [1,  'La Paz'],
            [2,  'Cochabamba'],
            [3,  'Santa Cruz de la Sierra'],
            [4,  'Oruro'],
            [5,  'Potosí'],
            [6,  'Sucre'],
            [7,  'Tarija'],
            [8,  'Trinidad'],
            [9,  'Cobija'],
            [10, 'El Alto'],
            [11, 'Sacaba'],
            [12, 'Quillacollo'],
            [13, 'Montero'],
            [14, 'Warnes'],
            [15, 'Riberalta'],
            [16, 'Yacuiba'],
            [17, 'Villazón'],
            [18, 'Tupiza'],
            [19, 'Llallagua'],
            [20, 'Camiri'],
            [21, 'Guayaramerín'],
            [22, 'Rurrenabaque'],
            [23, 'Otra ciudad'],
            [24, 'Extranjero'],
        ];

        $now = now();

        foreach ($ciudades as [$id, $nombre]) {
            DB::table('t_ciudad')->insertOrIgnore([
                'id_ciudad'    => $id,
                'id_us_reg'    => 1,
                'num_ciudad'   => $id,
                'nombre_ciudad'=> $nombre,
                'fecha_reg'    => $now,
                'estado'       => 1,
                'per_modificar'=> 0,
            ]);
        }

        $total = DB::table('t_ciudad')->count();
        $this->command->info("✓ {$total} ciudades en t_ciudad.");
    }
}
