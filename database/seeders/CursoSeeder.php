<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CursoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('t_programa')->whereNotIn('id_programa', [1])->delete();

        $now  = now()->toDateTimeString();
        $base = now();

        $cursos = [
            [
                'nombre_programa'          => 'Diplomado en Gestión Pública y Administración del Estado',
                'categoria_web_id'         => 1,
                'descripcion'              => 'Programa académico especializado en gestión pública, diseñado para servidores y funcionarios del sector estatal. Aborda los fundamentos del derecho administrativo, planificación estratégica, gestión de recursos humanos y control gubernamental.',
                'objetivo'                 => 'Desarrollar competencias técnicas y habilidades directivas en los participantes para el ejercicio eficiente de la función pública boliviana.',
                'dirigido'                 => 'Servidores públicos, funcionarios de instituciones estatales, profesionales del sector privado con interés en el área pública y egresados universitarios.',
                'requisitos'               => 'Título en Provisión Nacional a nivel licenciatura. Fotocopia del carnet de identidad vigente. Currículum vitae actualizado.',
                'inversion'                => 'Bs. 2.800 (pago en cuotas disponible). Consultar descuentos para servidores públicos y grupos.',
                'creditaje'                => '30 créditos académicos USFA',
                'nota'                     => 'Modalidad híbrida: clases presenciales los sábados y material asincrónico durante la semana.',
                'inicio_actividades'       => '2026-05-10',
                'finalizacion_actividades' => '2026-09-27',
                'inicio_inscripciones'     => '2026-04-15',
                'destacado'                => true,
                'orden'                    => 1,
                'meta_titulo'              => 'Diplomado en Gestión Pública | CENEFCO - USFA',
                'meta_descripcion'         => 'Certifícate en gestión pública con aval de la USFA. Modalidad híbrida, 30 créditos académicos.',
            ],
            [
                'nombre_programa'          => 'Diplomado en Derecho Empresarial y Contratos Comerciales',
                'categoria_web_id'         => 1,
                'descripcion'              => 'Formación especializada en derecho corporativo, contratos mercantiles, sociedades comerciales y regulación empresarial boliviana. Combina teoría jurídica con casos prácticos del entorno empresarial nacional.',
                'objetivo'                 => 'Capacitar a los participantes en la redacción, negociación y análisis de contratos comerciales, y en la gestión jurídica de empresas bajo la normativa boliviana.',
                'dirigido'                 => 'Abogados, gerentes, empresarios, administradores de empresas y profesionales que intervienen en procesos contractuales.',
                'requisitos'               => 'Título universitario en cualquier área. CI vigente. CV actualizado.',
                'inversion'                => 'Bs. 3.200 — financiamiento disponible en 3 cuotas sin interés.',
                'creditaje'                => '32 créditos académicos USFA',
                'nota'                     => 'Incluye acceso a plataforma virtual y biblioteca digital jurídica durante todo el programa.',
                'inicio_actividades'       => '2026-05-23',
                'finalizacion_actividades' => '2026-10-17',
                'inicio_inscripciones'     => '2026-04-28',
                'destacado'                => false,
                'orden'                    => 2,
                'meta_titulo'              => 'Diplomado en Derecho Empresarial | CENEFCO',
                'meta_descripcion'         => 'Especialízate en contratos comerciales y derecho corporativo boliviano con aval USFA.',
            ],
            [
                'nombre_programa'          => 'Curso de Excel Avanzado para Negocios y Finanzas',
                'categoria_web_id'         => 2,
                'descripcion'              => 'Domina las herramientas más potentes de Microsoft Excel aplicadas al análisis financiero, gestión de datos empresariales y automatización de reportes. Aprende tablas dinámicas, macros VBA y dashboards interactivos.',
                'objetivo'                 => 'Lograr que los participantes manejen Excel a nivel avanzado para optimizar procesos de análisis y toma de decisiones en el entorno empresarial.',
                'dirigido'                 => 'Contadores, administradores, analistas financieros, emprendedores y cualquier profesional que trabaje con datos.',
                'requisitos'               => 'Conocimientos básicos de Excel. Contar con Microsoft Excel 2019 o Microsoft 365 instalado.',
                'inversion'                => 'Bs. 450 — pago único. Incluye material digital y certificado de conclusión.',
                'creditaje'                => null,
                'nota'                     => 'Duración: 20 horas. Clases en vivo los martes y jueves de 19:00 a 21:00.',
                'inicio_actividades'       => '2026-05-06',
                'finalizacion_actividades' => '2026-05-28',
                'inicio_inscripciones'     => '2026-04-20',
                'destacado'                => true,
                'orden'                    => 3,
                'meta_titulo'              => 'Curso Excel Avanzado | CENEFCO',
                'meta_descripcion'         => 'Aprende Excel avanzado con tablas dinámicas, macros y dashboards aplicados a finanzas y negocios.',
            ],
            [
                'nombre_programa'          => 'Curso de Contabilidad Básica para Emprendedores',
                'categoria_web_id'         => 2,
                'descripcion'              => 'Programa práctico que enseña los fundamentos contables necesarios para llevar las finanzas de un negocio propio. Registro de ingresos y egresos, elaboración de estados financieros básicos y cumplimiento tributario con el SIN.',
                'objetivo'                 => 'Dotar a emprendedores y pequeños empresarios de las herramientas contables básicas para gestionar sus propios negocios con orden y transparencia.',
                'dirigido'                 => 'Emprendedores, dueños de microempresas, vendedores y personas que inician o tienen un negocio propio.',
                'requisitos'               => 'No se requieren conocimientos previos de contabilidad. Solo disposición de aprender.',
                'inversion'                => 'Bs. 320 — incluye material impreso y digital.',
                'creditaje'                => null,
                'nota'                     => 'Duración: 16 horas. Sábados de 9:00 a 13:00.',
                'inicio_actividades'       => '2026-05-16',
                'finalizacion_actividades' => '2026-06-06',
                'inicio_inscripciones'     => '2026-04-28',
                'destacado'                => false,
                'orden'                    => 4,
                'meta_titulo'              => 'Curso de Contabilidad para Emprendedores | CENEFCO',
                'meta_descripcion'         => 'Aprende contabilidad básica y maneja las finanzas de tu negocio con confianza.',
            ],
            [
                'nombre_programa'          => 'Taller de Oratoria y Comunicación Efectiva',
                'categoria_web_id'         => 3,
                'descripcion'              => 'Taller intensivo y práctico para superar el miedo escénico, mejorar la expresión verbal y no verbal, y dominar técnicas de persuasión y presentaciones en público. Ejercicios en vivo con retroalimentación inmediata.',
                'objetivo'                 => 'Que cada participante gane confianza y seguridad al hablar en público, aplicando técnicas de oratoria en contextos profesionales y cotidianos.',
                'dirigido'                 => 'Docentes, líderes, vendedores, directivos, estudiantes y cualquier persona que quiera mejorar su comunicación.',
                'requisitos'               => 'Ninguno. Solo ganas de participar activamente.',
                'inversion'                => 'Bs. 180 — incluye certificado de participación.',
                'creditaje'                => null,
                'nota'                     => 'Taller de un día completo: sábado de 8:00 a 17:00. Cupos limitados a 20 personas.',
                'inicio_actividades'       => '2026-05-30',
                'finalizacion_actividades' => '2026-05-30',
                'inicio_inscripciones'     => '2026-04-28',
                'destacado'                => false,
                'orden'                    => 5,
                'meta_titulo'              => 'Taller de Oratoria | CENEFCO',
                'meta_descripcion'         => 'Taller intensivo de oratoria y comunicación. Supera el miedo escénico en un día.',
            ],
            [
                'nombre_programa'          => 'Maestría en Administración de Empresas (MBA)',
                'categoria_web_id'         => 4,
                'descripcion'              => 'Programa de posgrado de alto nivel académico orientado a formar líderes empresariales con visión estratégica, capacidad analítica y habilidades directivas para afrontar los desafíos del mundo empresarial contemporáneo.',
                'objetivo'                 => 'Formar profesionales con sólidas competencias en gestión estratégica, liderazgo organizacional, innovación y emprendimiento, preparados para asumir posiciones directivas.',
                'dirigido'                 => 'Profesionales con título universitario y mínimo 2 años de experiencia laboral, que buscan un crecimiento acelerado en su carrera directiva.',
                'requisitos'               => 'Título universitario (Licenciatura). Mínimo 2 años de experiencia profesional. CI vigente. CV actualizado. Carta de motivación. Fotografía de estudio 3×3.',
                'inversion'                => 'Bs. 12.500 — financiamiento en 10 cuotas mensuales. Becas parciales disponibles.',
                'creditaje'                => '60 créditos académicos USFA',
                'nota'                     => 'Modalidad: viernes por la tarde y sábado por la mañana. Duración: 18 meses.',
                'inicio_actividades'       => '2026-06-06',
                'finalizacion_actividades' => '2027-12-05',
                'inicio_inscripciones'     => '2026-04-28',
                'destacado'                => true,
                'orden'                    => 6,
                'meta_titulo'              => 'MBA — Maestría en Administración de Empresas | CENEFCO - USFA',
                'meta_descripcion'         => 'Lleva tu carrera al siguiente nivel con el MBA de CENEFCO. 60 créditos, aval USFA.',
            ],
            [
                'nombre_programa'          => 'Especialización en Marketing Digital y E-Commerce',
                'categoria_web_id'         => 5,
                'descripcion'              => 'Programa especializado que cubre las principales estrategias del marketing digital: SEO, redes sociales, publicidad pagada (Meta Ads, Google Ads), email marketing y gestión de tiendas online. Enfoque práctico con casos reales del mercado boliviano.',
                'objetivo'                 => 'Que los participantes diseñen, ejecuten y midan campañas digitales efectivas, y gestionen canales de venta online con resultados medibles.',
                'dirigido'                 => 'Profesionales de marketing, emprendedores digitales, community managers y dueños de negocios que venden o quieren vender por internet.',
                'requisitos'               => 'Título universitario o técnico. Acceso a smartphone o computadora con internet. CI vigente.',
                'inversion'                => 'Bs. 1.800 — 3 cuotas disponibles.',
                'creditaje'                => '20 créditos académicos USFA',
                'nota'                     => 'Incluye licencia de herramientas digitales durante el programa y acompañamiento post-conclusión por 30 días.',
                'inicio_actividades'       => '2026-05-16',
                'finalizacion_actividades' => '2026-08-08',
                'inicio_inscripciones'     => '2026-04-28',
                'destacado'                => true,
                'orden'                    => 7,
                'meta_titulo'              => 'Especialización en Marketing Digital | CENEFCO',
                'meta_descripcion'         => 'Domina el marketing digital y e-commerce con herramientas reales. Aval USFA.',
            ],
            [
                'nombre_programa'          => 'Seminario de Actualización Tributaria 2026 — NIT, Facturas y Declaraciones',
                'categoria_web_id'         => 6,
                'descripcion'              => 'Seminario de actualización sobre las últimas modificaciones al sistema tributario boliviano: régimen de facturación electrónica, declaraciones juradas, auditoría fiscal y novedades del Servicio de Impuestos Nacionales (SIN) para la gestión 2026.',
                'objetivo'                 => 'Mantener actualizados a contadores, administradores y empresarios sobre la normativa tributaria vigente y evitar contingencias con el SIN.',
                'dirigido'                 => 'Contadores, auditores, administradores de empresas, gerentes financieros y emprendedores con obligaciones tributarias.',
                'requisitos'               => 'Ninguno. Abierto al público en general.',
                'inversion'                => 'Bs. 120 — incluye coffee break y certificado de asistencia.',
                'creditaje'                => null,
                'nota'                     => 'Evento de medio día: viernes 13:00 a 18:00.',
                'inicio_actividades'       => '2026-05-22',
                'finalizacion_actividades' => '2026-05-22',
                'inicio_inscripciones'     => '2026-04-28',
                'destacado'                => false,
                'orden'                    => 8,
                'meta_titulo'              => 'Seminario Tributario 2026 | CENEFCO',
                'meta_descripcion'         => 'Actualízate en tributación boliviana 2026. Facturación electrónica, declaraciones y SIN.',
            ],
            [
                'nombre_programa'          => 'Certificación en Gestión de Proyectos — Fundamentos PMI',
                'categoria_web_id'         => 7,
                'descripcion'              => 'Programa de certificación que prepara a los participantes en los fundamentos de la gestión de proyectos según el estándar del Project Management Institute (PMI). Cubre el ciclo de vida del proyecto, gestión del alcance, tiempo, costo, calidad y riesgos.',
                'objetivo'                 => 'Que los participantes adquieran las bases conceptuales y prácticas de la gestión de proyectos profesional, preparándolos para entornos que aplican metodologías PM.',
                'dirigido'                 => 'Profesionales de cualquier área que lideran o participan en proyectos, ya sea en el sector público o privado.',
                'requisitos'               => 'Título universitario o técnico. CI vigente. Se valorará experiencia previa en proyectos.',
                'inversion'                => 'Bs. 950 — incluye material del PMI y certificado CENEFCO-USFA.',
                'creditaje'                => '12 créditos académicos USFA',
                'nota'                     => 'Duración: 40 horas. Sábados de 8:00 a 12:00 y de 14:00 a 18:00.',
                'inicio_actividades'       => '2026-05-09',
                'finalizacion_actividades' => '2026-06-27',
                'inicio_inscripciones'     => '2026-04-28',
                'destacado'                => false,
                'orden'                    => 9,
                'meta_titulo'              => 'Certificación en Gestión de Proyectos PMI | CENEFCO',
                'meta_descripcion'         => 'Certifícate en gestión de proyectos con fundamentos PMI. Aval USFA.',
            ],
        ];

        $nextId = (DB::table('t_programa')->max('id_programa') ?? 0) + 1;

        foreach ($cursos as $curso) {
            $slug = Str::slug($curso['nombre_programa']);
            $existeSlug = DB::table('t_programa')->where('slug', $slug)->exists();
            if ($existeSlug) {
                $slug .= '-' . $nextId;
            }

            DB::table('t_programa')->insertOrIgnore([
                'id_programa'              => $nextId,
                'id_us_reg'               => 0,
                'num_programa'            => $nextId,
                'nombre_programa'         => $curso['nombre_programa'],
                'slug'                    => $slug,
                'descripcion'             => $curso['descripcion'],
                'objetivo'                => $curso['objetivo'],
                'dirigido'                => $curso['dirigido'],
                'requisitos'              => $curso['requisitos'],
                'inversion'               => $curso['inversion'],
                'creditaje'               => $curso['creditaje'] ?? null,
                'nota'                    => $curso['nota'] ?? null,
                'inicio_actividades'      => $curso['inicio_actividades'],
                'finalizacion_actividades'=> $curso['finalizacion_actividades'],
                'inicio_inscripciones'    => $curso['inicio_inscripciones'],
                'categoria_web_id'        => $curso['categoria_web_id'],
                'destacado'               => $curso['destacado'] ? 1 : 0,
                'orden'                   => $curso['orden'],
                'estado'                  => 1,
                'estado_web'              => 'publicado',
                'fecha_publicacion'       => now()->toDateTimeString(),
                'meta_titulo'             => $curso['meta_titulo'],
                'meta_descripcion'        => $curso['meta_descripcion'],
                'url_video'               => null,
                'url_whatsapp'            => null,
                'foto'                    => null,
                'fecha_reg'               => $now,
                'updated_at'              => $now,
            ]);

            $nextId++;
        }

        $total = DB::table('t_programa')->where('estado_web', 'publicado')->count();
        $this->command->info("✓ {$total} cursos publicados en t_programa.");
    }
}
