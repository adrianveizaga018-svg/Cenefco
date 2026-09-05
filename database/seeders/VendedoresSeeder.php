<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendedoresSeeder extends Seeder
{
    public function run(): void
    {
        $vendedores = [
            [
                'nombre'      => 'Paola',
                'apellido'    => 'Geleni',
                'ci'          => '7845123',
                'telefono'    => '70123456',
                'email'       => 'paola.geleni@cenefco.edu.bo',
                'foto'        => null,
                'pagina'      => 'FORMET',
                'meta_ventas' => 6000.00,
                'comision'    => 8.00,
                'activo'      => true,
                'usuario_id'  => null,
            ],
            [
                'nombre'      => 'Jhonatan',
                'apellido'    => 'Mamani',
                'ci'          => '9234567',
                'telefono'    => '71234567',
                'email'       => 'jhonatan.mamani@cenefco.edu.bo',
                'foto'        => null,
                'pagina'      => 'VIRTUALTECH',
                'meta_ventas' => 8000.00,
                'comision'    => 10.00,
                'activo'      => true,
                'usuario_id'  => null,
            ],
            [
                'nombre'      => 'Jhonatan',
                'apellido'    => 'Quispe',
                'ci'          => '8456789',
                'telefono'    => '72345678',
                'email'       => 'jhonatan.quispe@cenefco.edu.bo',
                'foto'        => null,
                'pagina'      => 'TESLA CAMPUS',
                'meta_ventas' => 7500.00,
                'comision'    => 9.00,
                'activo'      => true,
                'usuario_id'  => null,
            ],
            [
                'nombre'      => 'María',
                'apellido'    => 'Condori',
                'ci'          => '6123456',
                'telefono'    => '73456789',
                'email'       => 'maria.condori@cenefco.edu.bo',
                'foto'        => null,
                'pagina'      => 'FORMET',
                'meta_ventas' => 5500.00,
                'comision'    => 7.50,
                'activo'      => true,
                'usuario_id'  => null,
            ],
            [
                'nombre'      => 'Carlos',
                'apellido'    => 'Flores',
                'ci'          => '5678901',
                'telefono'    => '74567890',
                'email'       => 'carlos.flores@cenefco.edu.bo',
                'foto'        => null,
                'pagina'      => 'VIRTUALTECH',
                'meta_ventas' => 4000.00,
                'comision'    => 6.00,
                'activo'      => false,
                'usuario_id'  => null,
            ],
        ];

        foreach ($vendedores as $vendedor) {
            $yaExiste = DB::table('vendedores')
                ->where('ci', $vendedor['ci'])
                ->exists();

            if ($yaExiste) {
                continue;
            }

            DB::table('vendedores')->insertOrIgnore([
                'nombre'      => $vendedor['nombre'],
                'apellido'    => $vendedor['apellido'],
                'ci'          => $vendedor['ci'],
                'telefono'    => $vendedor['telefono'],
                'email'       => $vendedor['email'],
                'foto'        => $vendedor['foto'],
                'pagina'      => $vendedor['pagina'],
                'meta_ventas' => $vendedor['meta_ventas'],
                'comision'    => $vendedor['comision'],
                'activo'      => $vendedor['activo'] ? 1 : 0,
                'usuario_id'  => $vendedor['usuario_id'],
                'created_at'  => now()->toDateTimeString(),
                'updated_at'  => now()->toDateTimeString(),
            ]);
        }

        $this->command->info('✓ VendedoresSeeder: ' . count($vendedores) . ' vendedores insertados.');
    }
}
