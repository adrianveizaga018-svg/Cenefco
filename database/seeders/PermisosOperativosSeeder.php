<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisosOperativosSeeder extends Seeder
{
    public function run(): void
    {
        $modulosNuevos = [
            'cert-config'      => ['ver', 'crear', 'editar', 'eliminar'],
            'cert-solicitudes' => ['ver', 'crear', 'editar', 'eliminar'],
            'gastos'           => ['ver', 'crear', 'editar', 'eliminar'],
            'campanas'         => ['ver', 'crear', 'editar', 'eliminar'],
            'empleados'        => ['ver', 'crear', 'editar', 'eliminar'],
            'planillas'        => ['ver', 'crear', 'editar', 'eliminar'],
            'honorarios'       => ['ver', 'crear', 'editar', 'eliminar'],
        ];

        foreach ($modulosNuevos as $modulo => $acciones) {
            foreach ($acciones as $accion) {
                $codigo = "{$modulo}.{$accion}";
                DB::table('permisos')->updateOrInsert(
                    ['codigo' => $codigo],
                    ['codigo' => $codigo, 'descripcion' => ucfirst($accion) . ' ' . str_replace('-', ' ', $modulo), 'modulo' => $modulo]
                );
            }
        }

        $coordinadorId = DB::table('roles')->where('nombre', 'coordinador')->value('id');

        if ($coordinadorId) {
            $permisosCoordinador = DB::table('permisos')
                ->where(function ($q) {
                    $q->where('modulo', 'cert-config')
                      ->orWhere('modulo', 'cert-solicitudes');
                })
                ->pluck('id');
            foreach ($permisosCoordinador as $permisoId) {
                DB::table('roles_permisos')->updateOrInsert(['rol_id' => $coordinadorId, 'permiso_id' => $permisoId]);
            }
        }

        $total = DB::table('permisos')->count();
        $this->command->info("✓ Catálogo de permisos operativos completo: {$total} permisos.");
    }
}
