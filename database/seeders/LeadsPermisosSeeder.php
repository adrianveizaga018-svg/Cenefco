<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadsPermisosSeeder extends Seeder
{
    public function run(): void
    {
        $codigos = [
            ['codigo' => 'leads.ver',      'descripcion' => 'Ver campañas de leads y sus contactos', 'modulo' => 'leads'],
            ['codigo' => 'leads.crear',    'descripcion' => 'Crear campañas de leads y registrar/importar contactos', 'modulo' => 'leads'],
            ['codigo' => 'leads.editar',   'descripcion' => 'Editar campañas de leads y sus contactos', 'modulo' => 'leads'],
            ['codigo' => 'leads.eliminar', 'descripcion' => 'Eliminar campañas de leads y sus contactos', 'modulo' => 'leads'],
        ];

        foreach ($codigos as $permiso) {
            DB::table('permisos')->updateOrInsert(['codigo' => $permiso['codigo']], $permiso);
        }

        $coordinadorId = DB::table('roles')->where('nombre', 'coordinador')->value('id');

        if ($coordinadorId) {
            $permisoIds = DB::table('permisos')
                ->whereIn('codigo', array_column($codigos, 'codigo'))
                ->pluck('id');
            foreach ($permisoIds as $permisoId) {
                DB::table('roles_permisos')->updateOrInsert(['rol_id' => $coordinadorId, 'permiso_id' => $permisoId]);
            }
        }

        $this->command?->info('✓ LeadsPermisosSeeder: permisos de leads creados y asignados.');
    }
}
