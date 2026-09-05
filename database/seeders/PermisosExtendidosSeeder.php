<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisosExtendidosSeeder extends Seeder
{
    public function run(): void
    {
        $modulosNuevos = [
            'whatsapp'        => ['ver', 'crear', 'editar', 'eliminar'],
            'moodle'          => ['ver', 'crear', 'editar', 'eliminar'],
            'cursos_migrados' => ['ver', 'crear', 'editar', 'eliminar'],
            'biblioteca'      => ['ver', 'crear', 'editar', 'eliminar'],
            'cartas'          => ['ver', 'crear', 'editar', 'eliminar'],
            'ventas'          => ['ver', 'crear', 'editar', 'eliminar'],
            'historial'       => ['ver', 'crear', 'editar', 'eliminar'],
            'asesorias'       => ['ver', 'crear', 'editar', 'eliminar'],
            'zoom'            => ['ver', 'crear', 'editar', 'eliminar'],
        ];

        foreach ($modulosNuevos as $modulo => $acciones) {
            foreach ($acciones as $accion) {
                $codigo = "{$modulo}.{$accion}";
                DB::table('permisos')->updateOrInsert(
                    ['codigo' => $codigo],
                    ['codigo' => $codigo, 'descripcion' => ucfirst($accion) . ' ' . str_replace('_', ' ', $modulo), 'modulo' => $modulo]
                );
            }
        }

        $codigosSueltos = [
            ['codigo' => 'notas.eliminar',      'descripcion' => 'Eliminar notas',              'modulo' => 'notas'],
            ['codigo' => 'contacto.crear',      'descripcion' => 'Crear mensajes de contacto',  'modulo' => 'contacto'],
            ['codigo' => 'sugerencias.crear',   'descripcion' => 'Crear sugerencias y reclamos', 'modulo' => 'sugerencias'],
        ];

        foreach ($codigosSueltos as $permiso) {
            DB::table('permisos')->updateOrInsert(['codigo' => $permiso['codigo']], $permiso);
        }

        $coordinadorId = DB::table('roles')->where('nombre', 'coordinador')->value('id');

        if ($coordinadorId) {
            $permisosCoordinador = DB::table('permisos')
                ->whereNotIn('codigo', ['usuarios.eliminar', 'configuracion.eliminar', 'devoluciones.aprobar'])
                ->pluck('id');
            foreach ($permisosCoordinador as $permisoId) {
                DB::table('roles_permisos')->updateOrInsert(['rol_id' => $coordinadorId, 'permiso_id' => $permisoId]);
            }
        }

        $total = DB::table('permisos')->count();
        $this->command->info("✓ Catálogo de permisos completo: {$total} permisos.");
    }
}
