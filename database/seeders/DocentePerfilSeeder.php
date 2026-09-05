<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocentePerfilSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        $docentes = [

            [
                'usuario_id'       => 9001,
                'nombre_completo'  => 'Alejandro Vargas Mendoza',
                'titulo_academico' => 'Lic.',
                'especialidad'     => null,
                'biografia'        => null,
                'foto_url'         => null,
                'foto_alt'         => null,
                'email_publico'    => 'alejandro.vargas@cenefco.bo',
                'linkedin_url'     => null,
                'tipo'             => 'titular',
                'mostrar_en_web'   => 1,
                'orden'            => 100,
                'estado'           => 'publicado',
            ],
            [
                'usuario_id'       => 9002,
                'nombre_completo'  => 'Patricia Morales Salinas',
                'titulo_academico' => null,
                'especialidad'     => null,
                'biografia'        => null,
                'foto_url'         => null,
                'foto_alt'         => null,
                'email_publico'    => 'patricia.morales@cenefco.bo',
                'linkedin_url'     => null,
                'tipo'             => 'titular',
                'mostrar_en_web'   => 1,
                'orden'            => 101,
                'estado'           => 'publicado',
            ],
            [
                'usuario_id'       => 9003,
                'nombre_completo'  => 'Fernando Ramos Ortega',
                'titulo_academico' => null,
                'especialidad'     => null,
                'biografia'        => null,
                'foto_url'         => null,
                'foto_alt'         => null,
                'email_publico'    => 'fernando.ramos@cenefco.bo',
                'linkedin_url'     => null,
                'tipo'             => 'titular',
                'mostrar_en_web'   => 1,
                'orden'            => 102,
                'estado'           => 'publicado',
            ],
            [
                'usuario_id'       => 9004,
                'nombre_completo'  => 'Claudia Soria Pedraza',
                'titulo_academico' => null,
                'especialidad'     => null,
                'biografia'        => null,
                'foto_url'         => null,
                'foto_alt'         => null,
                'email_publico'    => 'claudia.soria@cenefco.bo',
                'linkedin_url'     => null,
                'tipo'             => 'titular',
                'mostrar_en_web'   => 1,
                'orden'            => 103,
                'estado'           => 'publicado',
            ],
            [
                'usuario_id'       => 9005,
                'nombre_completo'  => 'Roberto Espinoza Cáceres',
                'titulo_academico' => null,
                'especialidad'     => null,
                'biografia'        => null,
                'foto_url'         => null,
                'foto_alt'         => null,
                'email_publico'    => 'roberto.espinoza@cenefco.bo',
                'linkedin_url'     => null,
                'tipo'             => 'titular',
                'mostrar_en_web'   => 1,
                'orden'            => 104,
                'estado'           => 'publicado',
            ],

            [
                'nombre_completo'  => 'Dr. Carlos Alberto Mendoza Torrez',
                'titulo_academico' => 'Doctor en Administración Pública — Universidad Mayor de San Andrés',
                'especialidad'     => 'Gestión Pública y Políticas de Estado',
                'biografia'        => 'Más de 20 años de experiencia en administración pública y gestión gubernamental. Consultor del Ministerio de Planificación y docente universitario. Ha publicado diversos artículos sobre modernización del Estado boliviano.',
                'foto_url'         => null,
                'foto_alt'         => 'Dr. Carlos Mendoza',
                'email_publico'    => 'c.mendoza@cenefco.bo',
                'linkedin_url'     => null,
                'tipo'             => 'titular',
                'mostrar_en_web'   => 1,
                'orden'            => 1,
                'estado'           => 'publicado',
            ],
            [
                'nombre_completo'  => 'Mgr. Ana Patricia Flores Condori',
                'titulo_academico' => 'Magíster en Derecho Administrativo — Universidad Católica Boliviana',
                'especialidad'     => 'Derecho Administrativo y Contratación Pública',
                'biografia'        => 'Especialista en derecho administrativo con amplia trayectoria en el sector público. Asesora legal de entidades gubernamentales y docente de postgrado en varias universidades del eje central.',
                'foto_url'         => null,
                'foto_alt'         => 'Mgr. Ana Flores',
                'email_publico'    => 'a.flores@cenefco.bo',
                'linkedin_url'     => null,
                'tipo'             => 'titular',
                'mostrar_en_web'   => 1,
                'orden'            => 2,
                'estado'           => 'publicado',
            ],
            [
                'nombre_completo'  => 'Lic. Roberto Salinas Molina',
                'titulo_academico' => 'Licenciado en Economía — Universidad Autónoma Gabriel René Moreno',
                'especialidad'     => 'Economía Fiscal y Presupuesto Público',
                'biografia'        => 'Economista con especialización en finanzas públicas. Ha ejercido como director de presupuestos en el gobierno departamental de Santa Cruz y participado en proyectos de reforma fiscal a nivel nacional.',
                'foto_url'         => null,
                'foto_alt'         => 'Lic. Roberto Salinas',
                'email_publico'    => 'r.salinas@cenefco.bo',
                'linkedin_url'     => null,
                'tipo'             => 'titular',
                'mostrar_en_web'   => 1,
                'orden'            => 3,
                'estado'           => 'publicado',
            ],
            [
                'nombre_completo'  => 'Dra. Sofía Bejarano Navia',
                'titulo_academico' => 'Doctora en Ciencias Jurídicas — Universidad Mayor de San Simón',
                'especialidad'     => 'Derecho Empresarial y Contratos Comerciales',
                'biografia'        => 'Doctora en ciencias jurídicas con especialización en derecho mercantil. Socia fundadora de un estudio jurídico en Cochabamba con más de 15 años de trayectoria en litigios corporativos y asesoría empresarial.',
                'foto_url'         => null,
                'foto_alt'         => 'Dra. Sofía Bejarano',
                'email_publico'    => 's.bejarano@cenefco.bo',
                'linkedin_url'     => null,
                'tipo'             => 'titular',
                'mostrar_en_web'   => 1,
                'orden'            => 4,
                'estado'           => 'publicado',
            ],
            [
                'nombre_completo'  => 'Ing. Diego Arce Espinoza',
                'titulo_academico' => 'Magíster en Gestión de Proyectos — UMSA / PMI Certified',
                'especialidad'     => 'Gestión de Proyectos y Metodologías Ágiles',
                'biografia'        => 'Ingeniero industrial con certificación PMP y más de 12 años liderando proyectos de transformación organizacional en empresas públicas y privadas. Instructor certificado en metodologías ágiles (Scrum, Kanban).',
                'foto_url'         => null,
                'foto_alt'         => 'Ing. Diego Arce',
                'email_publico'    => 'd.arce@cenefco.bo',
                'linkedin_url'     => null,
                'tipo'             => 'titular',
                'mostrar_en_web'   => 1,
                'orden'            => 5,
                'estado'           => 'publicado',
            ],
            [
                'nombre_completo'  => 'Mgr. Valeria Choque Apaza',
                'titulo_academico' => 'Magíster en Marketing Digital — Universidad Privada de Bolivia',
                'especialidad'     => 'Marketing Digital y Transformación Digital',
                'biografia'        => 'Especialista en marketing digital con experiencia en estrategias de posicionamiento, e-commerce y analítica web. Directora de proyectos digitales en agencia reconocida a nivel nacional y regional.',
                'foto_url'         => null,
                'foto_alt'         => 'Mgr. Valeria Choque',
                'email_publico'    => 'v.choque@cenefco.bo',
                'linkedin_url'     => null,
                'tipo'             => 'invitado',
                'mostrar_en_web'   => 1,
                'orden'            => 6,
                'estado'           => 'publicado',
            ],
            [
                'nombre_completo'  => 'Lic. Fernando Rojas Paredes',
                'titulo_academico' => 'Licenciado en Contaduría Pública — Universidad Autónoma Tomás Frías',
                'especialidad'     => 'Contabilidad Gubernamental y Normas NICSP',
                'biografia'        => 'Contador público con especialización en contabilidad del sector público. Auditor externo con experiencia en entidades estatales y ex funcionario de la Contraloría General del Estado.',
                'foto_url'         => null,
                'foto_alt'         => 'Lic. Fernando Rojas',
                'email_publico'    => 'f.rojas@cenefco.bo',
                'linkedin_url'     => null,
                'tipo'             => 'titular',
                'mostrar_en_web'   => 1,
                'orden'            => 7,
                'estado'           => 'publicado',
            ],
            [
                'nombre_completo'  => 'Dra. Patricia Vidal Cruz',
                'titulo_academico' => 'Doctora en Ciencias de la Educación — Universidad Pedagógica',
                'especialidad'     => 'Educación Superior y Gestión Académica',
                'biografia'        => 'Especialista en pedagogía universitaria y gestión de instituciones educativas. Rectora encargada de una unidad académica regional y docente con más de 18 años en formación de postgrado.',
                'foto_url'         => null,
                'foto_alt'         => 'Dra. Patricia Vidal',
                'email_publico'    => 'p.vidal@cenefco.bo',
                'linkedin_url'     => null,
                'tipo'             => 'invitado',
                'mostrar_en_web'   => 0,
                'orden'            => 8,
                'estado'           => 'archivado',
            ],
        ];

        $insertados = 0;
        foreach ($docentes as $d) {
            $existe = DB::table('web_docente_perfil')
                ->where('nombre_completo', $d['nombre_completo'])
                ->exists();

            if ($existe) {
                continue;
            }

            DB::table('web_docente_perfil')->insertOrIgnore(array_merge($d, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
            $insertados++;
        }

        $this->command->info("✓ Docentes: {$insertados} perfiles insertados en web_docente_perfil.");
    }
}
