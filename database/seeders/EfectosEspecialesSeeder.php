<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EfectosEspecialesSeeder extends Seeder
{
    public function run(): void
    {
        $efectos = [
            [
                'nombre'           => 'Navidad',
                'tipo_efecto'      => 'nieve',
                'color_primario'   => '#ffffff',
                'color_secundario' => '#b3d9ff',
                'fecha_inicio'     => '2026-12-23',
                'fecha_fin'        => '2026-12-26',
                'intensidad'       => 60,
                'activo'           => true,
            ],
            [
                'nombre'           => 'Año Nuevo',
                'tipo_efecto'      => 'confetti',
                'color_primario'   => '#FFD700',
                'color_secundario' => '#FF6B6B',
                'fecha_inicio'     => '2026-12-31',
                'fecha_fin'        => '2027-01-01',
                'intensidad'       => 80,
                'activo'           => true,
            ],
            [
                'nombre'           => 'Halloween',
                'tipo_efecto'      => 'hojas',
                'color_primario'   => '#FF6600',
                'color_secundario' => '#8B008B',
                'fecha_inicio'     => '2026-10-29',
                'fecha_fin'        => '2026-10-31',
                'intensidad'       => 50,
                'activo'           => true,
            ],
            [
                'nombre'           => 'Aniversario CENEFCO',
                'tipo_efecto'      => 'estrellas',
                'color_primario'   => '#FC8900',
                'color_secundario' => '#07435B',
                'fecha_inicio'     => '2026-08-01',
                'fecha_fin'        => '2026-08-03',
                'intensidad'       => 70,
                'activo'           => true,
            ],
        ];

        foreach ($efectos as $efecto) {
            $existe = DB::table('efectos_especiales')
                ->where('nombre', $efecto['nombre'])
                ->exists();
            if ($existe) continue;

            DB::table('efectos_especiales')->insertOrIgnore([
                ...$efecto,
                'activo'     => $efecto['activo'] ? 1 : 0,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);
        }

        $this->command->info('✓ EfectosEspecialesSeeder: ' . count($efectos) . ' efectos insertados.');
    }
}
