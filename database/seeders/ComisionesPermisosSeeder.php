<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComisionesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        $codigos = [
            ['codigo' => 'comisiones.ver',     'descripcion' => 'Ver comisiones de vendedores',              'modulo' => 'comisiones'],
            ['codigo' => 'comisiones.crear',   'descripcion' => 'Generar liquidaciones de comisión',         'modulo' => 'comisiones'],
            ['codigo' => 'comisiones.aprobar', 'descripcion' => 'Aprobar o anular liquidaciones de comisión', 'modulo' => 'comisiones'],
            ['codigo' => 'comisiones.pagar',   'descripcion' => 'Marcar una liquidación de comisión como pagada', 'modulo' => 'comisiones'],
        ];

        foreach ($codigos as $permiso) {
            DB::table('permisos')->updateOrInsert(['codigo' => $permiso['codigo']], $permiso);
        }

        $coordinadorId = DB::table('roles')->where('nombre', 'coordinador')->value('id');

        $permisoIds = DB::table('permisos')
            ->whereIn('codigo', array_column($codigos, 'codigo'))
            ->pluck('id');

        if ($coordinadorId) {
            foreach ($permisoIds as $permisoId) {
                DB::table('roles_permisos')->updateOrInsert(['rol_id' => $coordinadorId, 'permiso_id' => $permisoId]);
            }
        }

        $this->command?->info('✓ ComisionesPermisosSeeder: permisos de comisiones creados y asignados.');
    }
}
