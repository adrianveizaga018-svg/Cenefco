<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompromisosCobroPermisosSeeder extends Seeder
{
    public function run(): void
    {
        $codigos = [
            ['codigo' => 'compromisos-cobro.ver',    'descripcion' => 'Ver compromisos de cobro',                       'modulo' => 'compromisos-cobro'],
            ['codigo' => 'compromisos-cobro.crear',  'descripcion' => 'Registrar un compromiso de cobro',               'modulo' => 'compromisos-cobro'],
            ['codigo' => 'compromisos-cobro.editar', 'descripcion' => 'Reprogramar, cumplir o cancelar un compromiso de cobro', 'modulo' => 'compromisos-cobro'],
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

        $this->command?->info('✓ CompromisosCobroPermisosSeeder: permisos creados y asignados a coordinador.');
    }
}
