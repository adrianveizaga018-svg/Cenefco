<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre' => 'coordinador',   'descripcion' => 'Coordinador académico — gestiona programas e inscritos', 'activo' => true],
            ['nombre' => 'docente',       'descripcion' => 'Docente — carga notas y ve su curso asignado',           'activo' => true],
            ['nombre' => 'participante',  'descripcion' => 'Participante / estudiante — acceso a su área personal',  'activo' => true],
        ];

        foreach ($roles as $rol) {
            DB::table('roles')->updateOrInsert(
                ['nombre' => $rol['nombre']],
                $rol
            );
        }

        $modulos = [
            'programas'       => ['ver', 'crear', 'editar', 'eliminar'],
            'planes'          => ['ver', 'crear', 'editar', 'eliminar'],
            'inscripciones'   => ['ver', 'crear', 'editar', 'eliminar'],
            'devoluciones'    => ['aprobar'],
            'notas'           => ['ver', 'crear', 'editar'],
            'pagos'           => ['ver', 'crear', 'editar', 'eliminar'],
            'documentos'      => ['ver', 'crear', 'editar', 'eliminar'],
            'docentes'        => ['ver', 'crear', 'editar', 'eliminar'],
            'estudiantes'     => ['ver', 'crear', 'editar', 'eliminar'],
            'horarios'        => ['ver', 'crear', 'editar', 'eliminar'],
            'certificados'    => ['ver', 'crear', 'editar', 'eliminar'],
            'diplomados'      => ['ver', 'crear', 'editar', 'eliminar'],
            'directorio-archivos' => ['ver'],
            'reportes'        => ['ver'],
            'web'             => ['ver', 'crear', 'editar', 'eliminar'],
        ];

        foreach ($modulos as $modulo => $acciones) {
            foreach ($acciones as $accion) {
                $codigo = "{$modulo}.{$accion}";
                DB::table('permisos')->updateOrInsert(
                    ['codigo' => $codigo],
                    ['codigo' => $codigo, 'descripcion' => ucfirst($accion) . ' ' . $modulo, 'modulo' => $modulo]
                );
            }
        }

        $coordinadorId = DB::table('roles')->where('nombre', 'coordinador')->value('id');
        $docenteId     = DB::table('roles')->where('nombre', 'docente')->value('id');
        $participanteId= DB::table('roles')->where('nombre', 'participante')->value('id');

        $permisosCoordinador = DB::table('permisos')
            ->whereNotIn('codigo', ['usuarios.eliminar', 'configuracion.eliminar', 'devoluciones.aprobar'])
            ->pluck('id');
        foreach ($permisosCoordinador as $permisoId) {
            DB::table('roles_permisos')->updateOrInsert(
                ['rol_id' => $coordinadorId, 'permiso_id' => $permisoId]
            );
        }

        $permisosDocente = DB::table('permisos')
            ->whereIn('codigo', [
                'notas.ver', 'notas.crear', 'notas.editar',
                'horarios.ver',
                'inscripciones.ver',
                'estudiantes.ver',
                'programas.ver',
                'certificados.ver',
            ])
            ->pluck('id');
        foreach ($permisosDocente as $permisoId) {
            DB::table('roles_permisos')->updateOrInsert(
                ['rol_id' => $docenteId, 'permiso_id' => $permisoId]
            );
        }

        $permisosParticipante = DB::table('permisos')
            ->whereIn('codigo', [
                'programas.ver',
                'inscripciones.ver',
                'pagos.ver',
                'notas.ver',
                'certificados.ver',
            ])
            ->pluck('id');
        foreach ($permisosParticipante as $permisoId) {
            DB::table('roles_permisos')->updateOrInsert(
                ['rol_id' => $participanteId, 'permiso_id' => $permisoId]
            );
        }

        $totalRoles    = DB::table('roles')->count();
        $totalPermisos = DB::table('permisos')->count();
        $totalAsig     = DB::table('roles_permisos')->count();
        $this->command->info("✓ {$totalRoles} roles · {$totalPermisos} permisos · {$totalAsig} asignaciones en roles_permisos.");
    }
}
