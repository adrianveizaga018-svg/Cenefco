<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposBancoSeeder extends Seeder
{
    public function run(): void
    {
        $bancos = [
            'Banco Unión',
            'Banco Mercantil Santa Cruz',
            'Banco Ganadero',
            'Western Union',
            'Tigo Money',
        ];

        foreach ($bancos as $orden => $nombre) {
            DB::table('tipos_banco')->updateOrInsert(
                ['nombre' => $nombre],
                ['nombre' => $nombre, 'activo' => true, 'orden' => $orden, 'updated_at' => now()]
            );
        }

        $this->command->info('✓ Tipos de banco sembrados: ' . count($bancos));
    }
}
