<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Los roles de estudiante (participante, Ciudadano, Estudiante) ven su área personal
 * y nada del personal: antes "participante" tenía permisos de personal como
 * inscripciones.ver, con los que podía listar las inscripciones de todos.
 */
return new class extends Migration
{
    private const PERMISOS_ESTUDIANTE = [
        'estudiante.dashboard'    => 'Permite al estudiante ver su dashboard personal y estadísticas.',
        'estudiante.cursos'       => 'Permite al estudiante ver sus materias inscritas y grabaciones.',
        'estudiante.certificados' => 'Permite al estudiante ver y descargar sus certificados en PDF.',
        'estudiante.pagos'        => 'Permite al estudiante ver su historial de pagos y comprobantes.',
        'estudiante.oferta'       => 'Permite al estudiante ver el catálogo de cursos e inscribirse.',
    ];

    private const PERMISOS_PERSONAL = ['programas.ver', 'inscripciones.ver', 'pagos.ver', 'notas.ver', 'certificados.ver'];

    private const ROLES_ESTUDIANTE = ['participante', 'Ciudadano', 'Estudiante'];

    public function up(): void
    {
        foreach (self::PERMISOS_ESTUDIANTE as $codigo => $descripcion) {
            if (! DB::table('permisos')->where('codigo', $codigo)->exists()) {
                DB::table('permisos')->insert(['codigo' => $codigo, 'descripcion' => $descripcion, 'modulo' => 'Área del Estudiante']);
            }
        }

        $permisosEstudiante = DB::table('permisos')->whereIn('codigo', array_keys(self::PERMISOS_ESTUDIANTE))->pluck('id');
        $permisosPersonal   = DB::table('permisos')->whereIn('codigo', self::PERMISOS_PERSONAL)->pluck('id');

        foreach (DB::table('roles')->whereIn('nombre', self::ROLES_ESTUDIANTE)->pluck('id') as $rolId) {
            DB::table('roles_permisos')->where('rol_id', $rolId)->whereIn('permiso_id', $permisosPersonal)->delete();
            foreach ($permisosEstudiante as $permisoId) {
                DB::table('roles_permisos')->updateOrInsert(['rol_id' => $rolId, 'permiso_id' => $permisoId]);
            }
        }
    }

    public function down(): void
    {
        // Sin reversión: devolver permisos de personal a los estudiantes reabriría el acceso indebido.
    }
};
