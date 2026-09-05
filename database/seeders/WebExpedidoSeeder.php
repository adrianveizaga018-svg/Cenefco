<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebExpedidoSeeder extends Seeder
{
    public function run(): void
    {
        $departamentos = [
            ['nombre' => 'Beni',        'activo' => true, 'orden' => 1],
            ['nombre' => 'Chuquisaca',  'activo' => true, 'orden' => 2],
            ['nombre' => 'Cochabamba',  'activo' => true, 'orden' => 3],
            ['nombre' => 'La Paz',      'activo' => true, 'orden' => 4],
            ['nombre' => 'Oruro',       'activo' => true, 'orden' => 5],
            ['nombre' => 'Pando',       'activo' => true, 'orden' => 6],
            ['nombre' => 'Potosí',      'activo' => true, 'orden' => 7],
            ['nombre' => 'Santa Cruz',  'activo' => true, 'orden' => 8],
            ['nombre' => 'Tarija',      'activo' => true, 'orden' => 9],
            ['nombre' => 'Extranjero',  'activo' => true, 'orden' => 10],
        ];

        foreach ($departamentos as $dep) {
            DB::table('web_expedido')->updateOrInsert(
                ['nombre' => $dep['nombre']],
                $dep
            );
        }
    }
}
