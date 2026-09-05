<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            ['codigo' => '*',                      'descripcion' => 'Acceso total al sistema',                  'modulo' => 'sistema'],
            ['codigo' => 'usuarios.ver',           'descripcion' => 'Ver listado de usuarios',                  'modulo' => 'usuarios'],
            ['codigo' => 'usuarios.crear',         'descripcion' => 'Crear usuarios y roles',                   'modulo' => 'usuarios'],
            ['codigo' => 'usuarios.editar',        'descripcion' => 'Editar usuarios y roles',                  'modulo' => 'usuarios'],
            ['codigo' => 'usuarios.eliminar',      'descripcion' => 'Eliminar usuarios y roles',                'modulo' => 'usuarios'],

            ['codigo' => 'noticias.ver',           'descripcion' => 'Ver noticias y artículos',                 'modulo' => 'noticias'],
            ['codigo' => 'noticias.crear',         'descripcion' => 'Crear noticias, categorías y etiquetas',   'modulo' => 'noticias'],
            ['codigo' => 'noticias.editar',        'descripcion' => 'Editar noticias, categorías y etiquetas',  'modulo' => 'noticias'],
            ['codigo' => 'noticias.eliminar',      'descripcion' => 'Eliminar noticias, categorías y etiquetas', 'modulo' => 'noticias'],

            ['codigo' => 'eventos.ver',            'descripcion' => 'Ver eventos',                              'modulo' => 'eventos'],
            ['codigo' => 'eventos.crear',          'descripcion' => 'Crear eventos',                            'modulo' => 'eventos'],
            ['codigo' => 'eventos.editar',         'descripcion' => 'Editar eventos',                           'modulo' => 'eventos'],
            ['codigo' => 'eventos.eliminar',       'descripcion' => 'Eliminar eventos',                         'modulo' => 'eventos'],

            ['codigo' => 'contenido.ver',          'descripcion' => 'Ver contenido web',                        'modulo' => 'contenido'],
            ['codigo' => 'contenido.crear',        'descripcion' => 'Crear contenido web',                      'modulo' => 'contenido'],
            ['codigo' => 'contenido.editar',       'descripcion' => 'Editar contenido web',                     'modulo' => 'contenido'],
            ['codigo' => 'contenido.eliminar',     'descripcion' => 'Eliminar contenido web',                   'modulo' => 'contenido'],

            ['codigo' => 'contacto.ver',           'descripcion' => 'Ver mensajes de contacto',                 'modulo' => 'contacto'],
            ['codigo' => 'contacto.editar',        'descripcion' => 'Gestionar mensajes de contacto',           'modulo' => 'contacto'],
            ['codigo' => 'contacto.eliminar',      'descripcion' => 'Eliminar mensajes de contacto',            'modulo' => 'contacto'],

            ['codigo' => 'sugerencias.ver',        'descripcion' => 'Ver sugerencias y reclamos',               'modulo' => 'sugerencias'],
            ['codigo' => 'sugerencias.editar',     'descripcion' => 'Gestionar sugerencias y reclamos',         'modulo' => 'sugerencias'],
            ['codigo' => 'sugerencias.eliminar',   'descripcion' => 'Eliminar sugerencias y reclamos',          'modulo' => 'sugerencias'],

            ['codigo' => 'transparencia.ver',      'descripcion' => 'Ver documentos de transparencia',          'modulo' => 'transparencia'],
            ['codigo' => 'transparencia.crear',    'descripcion' => 'Subir documentos de transparencia',        'modulo' => 'transparencia'],
            ['codigo' => 'transparencia.editar',   'descripcion' => 'Editar documentos de transparencia',       'modulo' => 'transparencia'],
            ['codigo' => 'transparencia.eliminar', 'descripcion' => 'Eliminar documentos de transparencia',     'modulo' => 'transparencia'],

            ['codigo' => 'tramites.ver',           'descripcion' => 'Ver trámites',                             'modulo' => 'tramites'],
            ['codigo' => 'tramites.crear',         'descripcion' => 'Crear tipos de trámite',                   'modulo' => 'tramites'],
            ['codigo' => 'tramites.editar',        'descripcion' => 'Editar trámites',                          'modulo' => 'tramites'],
            ['codigo' => 'tramites.eliminar',      'descripcion' => 'Eliminar trámites',                        'modulo' => 'tramites'],

            ['codigo' => 'decretos.ver',           'descripcion' => 'Ver decretos municipales',                 'modulo' => 'decretos'],
            ['codigo' => 'decretos.crear',         'descripcion' => 'Crear decretos municipales',               'modulo' => 'decretos'],
            ['codigo' => 'decretos.editar',        'descripcion' => 'Editar decretos municipales',              'modulo' => 'decretos'],
            ['codigo' => 'decretos.eliminar',      'descripcion' => 'Eliminar decretos municipales',            'modulo' => 'decretos'],

            ['codigo' => 'secretarias.ver',        'descripcion' => 'Ver secretarías',                          'modulo' => 'secretarias'],
            ['codigo' => 'secretarias.crear',      'descripcion' => 'Crear secretarías',                        'modulo' => 'secretarias'],
            ['codigo' => 'secretarias.editar',     'descripcion' => 'Editar secretarías',                       'modulo' => 'secretarias'],
            ['codigo' => 'secretarias.eliminar',   'descripcion' => 'Eliminar secretarías',                     'modulo' => 'secretarias'],

            ['codigo' => 'configuracion.ver',      'descripcion' => 'Ver configuración del sitio',              'modulo' => 'configuracion'],
            ['codigo' => 'configuracion.editar',   'descripcion' => 'Editar configuración del sitio',           'modulo' => 'configuracion'],
            ['codigo' => 'configuracion.eliminar', 'descripcion' => 'Eliminar configuraciones',                 'modulo' => 'configuracion'],

            ['codigo' => 'informes',               'descripcion' => 'Acceso a informes y estadísticas',         'modulo' => 'informes'],

            ['codigo' => 'inscripciones.ver',      'descripcion' => 'Ver inscripciones',                        'modulo' => 'inscripciones'],
            ['codigo' => 'inscripciones.editar',   'descripcion' => 'Gestionar inscripciones',                  'modulo' => 'inscripciones'],
            ['codigo' => 'inscripciones.eliminar', 'descripcion' => 'Eliminar inscripciones',                   'modulo' => 'inscripciones'],

            ['codigo' => 'diplomados.ver',         'descripcion' => 'Ver formularios de inscripción a diplomado', 'modulo' => 'diplomados'],
            ['codigo' => 'diplomados.crear',       'descripcion' => 'Registrar inscripciones a diplomado',        'modulo' => 'diplomados'],
            ['codigo' => 'diplomados.editar',      'descripcion' => 'Gestionar inscripciones a diplomado',        'modulo' => 'diplomados'],
            ['codigo' => 'diplomados.eliminar',    'descripcion' => 'Eliminar inscripciones a diplomado',         'modulo' => 'diplomados'],

            ['codigo' => 'certificados.ver',       'descripcion' => 'Ver certificados emitidos',                'modulo' => 'certificados'],
            ['codigo' => 'certificados.crear',     'descripcion' => 'Generar certificados',                     'modulo' => 'certificados'],
            ['codigo' => 'certificados.editar',    'descripcion' => 'Editar/anular certificados',               'modulo' => 'certificados'],
            ['codigo' => 'certificados.eliminar',  'descripcion' => 'Eliminar certificados',                    'modulo' => 'certificados'],

            ['codigo' => 'trivia.ver',             'descripcion' => 'Ver categorías, niveles y preguntas de trivia', 'modulo' => 'trivia'],
            ['codigo' => 'trivia.crear',           'descripcion' => 'Crear categorías, niveles y preguntas de trivia', 'modulo' => 'trivia'],
            ['codigo' => 'trivia.editar',          'descripcion' => 'Editar categorías, niveles y preguntas de trivia', 'modulo' => 'trivia'],
            ['codigo' => 'trivia.eliminar',        'descripcion' => 'Eliminar categorías, niveles y preguntas de trivia', 'modulo' => 'trivia'],
        ];

        foreach ($permisos as $permiso) {
            DB::table('permisos')->updateOrInsert(
                ['codigo' => $permiso['codigo']],
                $permiso
            );
        }

        $roles = [
            [
                'nombre' => 'Admin',
                'descripcion' => 'Administrador con acceso total al sistema.',
                'activo' => true,
            ],
            [
                'nombre' => 'Operador',
                'descripcion' => 'Puede gestionar contenido pero no usuarios ni configuración.',
                'activo' => true,
            ],
            [
                'nombre' => 'Ciudadano',
                'descripcion' => 'Usuario registrado desde el portal web.',
                'activo' => true,
            ],
        ];

        foreach ($roles as $rol) {
            DB::table('roles')->updateOrInsert(['nombre' => $rol['nombre']], $rol);
        }

        $adminRolId = DB::table('roles')->where('nombre', 'Admin')->value('id');
        $operadorRolId = DB::table('roles')->where('nombre', 'Operador')->value('id');

        $comodinId = DB::table('permisos')->where('codigo', '*')->value('id');
        DB::table('roles_permisos')->updateOrInsert(
            ['rol_id' => $adminRolId, 'permiso_id' => $comodinId],
            ['rol_id' => $adminRolId, 'permiso_id' => $comodinId]
        );

        $permisosOperador = [
            'noticias.ver', 'noticias.crear', 'noticias.editar',
            'eventos.ver',  'eventos.crear',  'eventos.editar',
            'contenido.ver', 'contenido.crear', 'contenido.editar',
            'contacto.ver', 'sugerencias.ver', 'inscripciones.ver',
            'diplomados.ver',
            'certificados.ver',
            'trivia.ver', 'trivia.crear', 'trivia.editar',
        ];

        foreach ($permisosOperador as $codigo) {
            $permisoId = DB::table('permisos')->where('codigo', $codigo)->value('id');
            if ($permisoId) {
                DB::table('roles_permisos')->updateOrInsert(
                    ['rol_id' => $operadorRolId, 'permiso_id' => $permisoId],
                    ['rol_id' => $operadorRolId, 'permiso_id' => $permisoId]
                );
            }
        }

        $adminEmail = env('ADMIN_EMAIL', 'admin@cenefco.bo');

        $adminId = DB::table('usuarios')->where('email', $adminEmail)->value('id');

        if (! $adminId) {
            $adminId = DB::table('usuarios')->insertGetId([
                'nombre' => 'Administrador',
                'apellido' => 'Sistema',
                'email' => $adminEmail,
                'password_hash' => Hash::make(env('ADMIN_PASSWORD', 'Admin1234!')),
                'tipo' => 'admin',
                'activo' => true,
                'email_verificado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('usuarios_roles')->updateOrInsert(
            ['usuario_id' => $adminId, 'rol_id' => $adminRolId],
            ['usuario_id' => $adminId, 'rol_id' => $adminRolId, 'asignado_at' => now()]
        );

        $this->command->info("✓ Admin creado: {$adminEmail}");
        $this->command->warn('  Contraseña: '.env('ADMIN_PASSWORD', 'Admin1234!'));
        $this->command->warn('  Cambia la contraseña en cuanto inicies sesión.');
    }
}
