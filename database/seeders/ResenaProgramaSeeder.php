<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResenaProgramaSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        $resenas = [
            
            [
                'programa_id'  => 1,
                'nombre'       => 'Carlos Alberto Mamani Quispe',
                'cargo_actual' => 'Jefe de Planificación — Gobierno Autónomo Municipal',
                'calificacion' => 5,
                'titulo_resena'=> 'Excelente formación, aplicable desde el primer día',
                'resena'       => 'El Diplomado en Gestión Pública superó mis expectativas. Los docentes tienen una sólida trayectoria en el sector público y los casos prácticos son 100% aplicables a la realidad boliviana. Recomiendo este programa a cualquier servidor público que quiera mejorar su desempeño.',
                'estado'       => 'aprobado',
                'verificado'   => true,
                'destacada'    => true,
            ],
            [
                'programa_id'  => 1,
                'nombre'       => 'Sonia Patricia Ticona',
                'cargo_actual' => 'Directora Administrativa — Ministerio de Economía',
                'calificacion' => 5,
                'titulo_resena'=> 'Contenido actualizado y docentes expertos',
                'resena'       => 'Muy buen programa. El módulo de control gubernamental y auditoría fue especialmente valioso. La modalidad híbrida me permitió compaginarlo con mi horario de trabajo sin problemas. El material virtual es de calidad y siempre estuvo disponible.',
                'estado'       => 'aprobado',
                'verificado'   => true,
                'destacada'    => false,
            ],
            [
                'programa_id'  => 1,
                'nombre'       => 'René Gustavo Mamani',
                'cargo_actual' => 'Técnico de Proyectos — Gobierno Departamental',
                'calificacion' => 4,
                'titulo_resena'=> 'Buen programa, algún tema podría profundizarse más',
                'resena'       => 'En general el diplomado es muy completo. El módulo de gestión de recursos humanos en el sector público podría tener más horas. Los demás módulos están muy bien estructurados y los docentes responden dudas con seriedad.',
                'estado'       => 'aprobado',
                'verificado'   => false,
                'destacada'    => false,
            ],

            
            [
                'programa_id'  => 2,
                'nombre'       => 'Ana María Flores Condori',
                'cargo_actual' => 'Abogada independiente — Estudio Jurídico Flores & Asociados',
                'calificacion' => 5,
                'titulo_resena'=> 'Imprescindible para abogados que trabajan con empresas',
                'resena'       => 'Este diplomado me dio las herramientas que necesitaba para asesorar contratos comerciales con confianza. La bibliografía es actual y los ejercicios prácticos de redacción de contratos son de alto nivel. El aval de la USFA le da mucho peso al diploma.',
                'estado'       => 'aprobado',
                'verificado'   => true,
                'destacada'    => true,
            ],
            [
                'programa_id'  => 2,
                'nombre'       => 'Marco Antonio Serrudo',
                'cargo_actual' => 'Gerente General — Empresa constructora',
                'calificacion' => 4,
                'titulo_resena'=> 'Muy útil para empresarios sin formación jurídica',
                'resena'       => 'Como empresario sin formación en derecho, este diplomado me abrió los ojos a muchos riesgos legales que ignoraba. Ahora puedo revisar contratos y negociar con mayor seguridad. Recomendaría agregar un módulo sobre derecho laboral más extenso.',
                'estado'       => 'aprobado',
                'verificado'   => false,
                'destacada'    => false,
            ],

            
            [
                'programa_id'  => 3,
                'nombre'       => 'Luis Eduardo Gutierrez Vargas',
                'cargo_actual' => 'Analista Financiero — Banco Unión S.A.',
                'calificacion' => 5,
                'titulo_resena'=> 'El mejor curso de Excel que he tomado',
                'resena'       => 'Llevaba años usando Excel de forma básica. Con este curso aprendí tablas dinámicas, macros VBA y dashboards interactivos que aplico todos los días en mi trabajo. El ritmo del curso es ideal y el docente explica de forma muy clara.',
                'estado'       => 'aprobado',
                'verificado'   => true,
                'destacada'    => true,
            ],
            [
                'programa_id'  => 3,
                'nombre'       => 'Paola Arancibia',
                'cargo_actual' => 'Contadora — PYME familiar',
                'calificacion' => 5,
                'titulo_resena'=> 'Cambió totalmente mi forma de trabajar',
                'resena'       => 'Antes tardaba horas haciendo reportes. Ahora los genero en minutos con las plantillas y automatizaciones que aprendí. El curso es corto pero muy intenso y práctico. 100% recomendable para contadores y administrativos.',
                'estado'       => 'aprobado',
                'verificado'   => true,
                'destacada'    => false,
            ],
            [
                'programa_id'  => 3,
                'nombre'       => 'Rodrigo Espinoza',
                'cargo_actual' => 'Emprendedor',
                'calificacion' => 4,
                'titulo_resena'=> 'Muy completo, necesita un poco más de tiempo en VBA',
                'resena'       => 'El curso cubre mucho contenido en poco tiempo. Hubiera preferido más horas dedicadas a macros VBA porque es el tema que más aplico. El resto está muy bien explicado. El certificado de conclusión se entregó rápido.',
                'estado'       => 'aprobado',
                'verificado'   => false,
                'destacada'    => false,
            ],

            
            [
                'programa_id'  => 4,
                'nombre'       => 'María Elena Choque Apaza',
                'cargo_actual' => 'Dueña de tienda de ropa — Mercado Las Américas',
                'calificacion' => 5,
                'titulo_resena'=> 'Por fin entiendo las finanzas de mi negocio',
                'resena'       => 'Nunca había llevado contabilidad formal en mi negocio. Este curso me enseñó a registrar ingresos y egresos, hacer mis declaraciones al SIN y entender si estoy ganando o perdiendo. El docente explica en lenguaje simple, sin tecnicismos.',
                'estado'       => 'aprobado',
                'verificado'   => true,
                'destacada'    => true,
            ],
            [
                'programa_id'  => 4,
                'nombre'       => 'Freddy Colque',
                'cargo_actual' => 'Vendedor de repuestos automotrices',
                'calificacion' => 5,
                'titulo_resena'=> 'Ideal para quienes tienen negocio propio',
                'resena'       => 'No sabía nada de contabilidad y ahora puedo llevar mis propios libros. El tema del SIN y las facturas fue el más útil para mí. El precio es muy accesible y los sábados son perfectos para quien trabaja entre semana.',
                'estado'       => 'aprobado',
                'verificado'   => false,
                'destacada'    => false,
            ],

            
            [
                'programa_id'  => 5,
                'nombre'       => 'Jorge Torrez Limachi',
                'cargo_actual' => 'Docente universitario — UMSA',
                'calificacion' => 5,
                'titulo_resena'=> 'Superé el miedo escénico en un solo día',
                'resena'       => 'Llegué temblando al taller y salí dando mi mejor discurso frente a todos. La metodología del facilitador es dinámica y se nota que tiene mucha experiencia. Los ejercicios de voz, postura y persuasión son muy prácticos. Un día bien invertido.',
                'estado'       => 'aprobado',
                'verificado'   => true,
                'destacada'    => true,
            ],
            [
                'programa_id'  => 5,
                'nombre'       => 'Viviana Quispe',
                'cargo_actual' => 'Estudiante universitaria',
                'calificacion' => 4,
                'titulo_resena'=> 'Muy útil para presentaciones y exposiciones',
                'resena'       => 'El taller me ayudó mucho para perder el miedo a hablar en público. Me hubiera gustado un segundo día para practicar más. El ambiente fue muy amigable y el facilitador fue muy paciente con todos. Lo recomendaría a mis compañeros.',
                'estado'       => 'aprobado',
                'verificado'   => false,
                'destacada'    => false,
            ],

            
            [
                'programa_id'  => 6,
                'nombre'       => 'Claudia Quispe Huanca',
                'cargo_actual' => 'Gerente de Operaciones — Empresa exportadora',
                'calificacion' => 5,
                'titulo_resena'=> 'La mejor inversión en mi carrera profesional',
                'resena'       => 'El MBA de CENEFCO-USFA transformó mi visión empresarial. Los casos de estudio son reales y relevantes para el mercado boliviano. El networking con los compañeros y docentes también ha sido invaluable. Recomiendo este programa sin dudarlo.',
                'estado'       => 'aprobado',
                'verificado'   => true,
                'destacada'    => true,
            ],
            [
                'programa_id'  => 6,
                'nombre'       => 'Fernando Ramos Ortega',
                'cargo_actual' => 'Director Comercial — Grupo empresarial',
                'calificacion' => 5,
                'titulo_resena'=> 'Nivel académico comparable con universidades privadas top',
                'resena'       => 'La calidad de los docentes y el contenido del MBA me sorprendió gratamente. Los módulos de finanzas corporativas y estrategia competitiva son especialmente sólidos. El aval de la USFA fue el factor decisivo para elegir este programa.',
                'estado'       => 'aprobado',
                'verificado'   => true,
                'destacada'    => false,
            ],
            [
                'programa_id'  => 6,
                'nombre'       => 'Roberto Méndez',
                'cargo_actual' => 'Consultor independiente',
                'calificacion' => 4,
                'titulo_resena'=> 'Excelente programa, exige mucha dedicación',
                'resena'       => 'El MBA es de alto nivel pero requiere mucho tiempo y compromiso. Para alguien que trabaja tiempo completo puede ser intenso. Aun así vale la pena cada esfuerzo. Los docentes son accesibles y siempre están disponibles para resolver dudas.',
                'estado'       => 'aprobado',
                'verificado'   => false,
                'destacada'    => false,
            ],

            
            [
                'programa_id'  => 7,
                'nombre'       => 'Sofía Mendoza Cabrera',
                'cargo_actual' => 'Community Manager — Agencia digital',
                'calificacion' => 5,
                'titulo_resena'=> 'Me dio las bases para crecer en marketing digital',
                'resena'       => 'El programa es muy práctico y actualizado. Aprendí a crear campañas en Meta Ads y Google Ads con resultados reales. Las herramientas incluidas durante el programa fueron un plus enorme. Ahora gestiono cuentas de clientes con mucha más confianza.',
                'estado'       => 'aprobado',
                'verificado'   => true,
                'destacada'    => true,
            ],
            [
                'programa_id'  => 7,
                'nombre'       => 'Diego Arce Espinoza',
                'cargo_actual' => 'Dueño de tienda online',
                'calificacion' => 5,
                'titulo_resena'=> 'Dupliqué mis ventas aplicando lo aprendido',
                'resena'       => 'Implementé las estrategias de SEO y publicidad pagada apenas terminé el programa y mis ventas online crecieron significativamente. El módulo de e-commerce es muy completo. El soporte post-programa de 30 días fue muy útil.',
                'estado'       => 'aprobado',
                'verificado'   => true,
                'destacada'    => false,
            ],

            
            [
                'programa_id'  => 8,
                'nombre'       => 'Patricia Vidal Cruz',
                'cargo_actual' => 'Contadora Auditora — Firma contable',
                'calificacion' => 5,
                'titulo_resena'=> 'Exactamente lo que necesitaba para actualizarme',
                'resena'       => 'El seminario cubrió todas las novedades del sistema tributario 2026. Las explicaciones sobre facturación electrónica y declaraciones juradas fueron clarísimas. Excelente expositor y muy buen material de apoyo. Un medio día muy bien aprovechado.',
                'estado'       => 'aprobado',
                'verificado'   => true,
                'destacada'    => true,
            ],
            [
                'programa_id'  => 8,
                'nombre'       => 'Álvaro Huanca',
                'cargo_actual' => 'Administrador de empresa',
                'calificacion' => 4,
                'titulo_resena'=> 'Información actualizada y práctica',
                'resena'       => 'El seminario es breve pero muy denso en contenido. Me hubiera gustado más tiempo para preguntas al final. El expositor conoce muy bien la normativa del SIN y los ejemplos prácticos ayudaron mucho a entender los cambios.',
                'estado'       => 'aprobado',
                'verificado'   => false,
                'destacada'    => false,
            ],

            
            [
                'programa_id'  => 9,
                'nombre'       => 'Roberto Salinas Molina',
                'cargo_actual' => 'Project Manager — Empresa de tecnología',
                'calificacion' => 5,
                'titulo_resena'=> 'Fundamentos sólidos para gestionar proyectos',
                'resena'       => 'El programa cubre perfectamente el marco del PMBOK. Los simulacros de examen y los casos prácticos son muy útiles. El certificado CENEFCO-USFA me ayudó a conseguir un ascenso. Recomiendo este programa a todo profesional que gestione proyectos.',
                'estado'       => 'aprobado',
                'verificado'   => true,
                'destacada'    => true,
            ],
            [
                'programa_id'  => 9,
                'nombre'       => 'Valeria Bejarano Navia',
                'cargo_actual' => 'Coordinadora de proyectos — ONG',
                'calificacion' => 4,
                'titulo_resena'=> 'Muy buena base para quienes inician en gestión de proyectos',
                'resena'       => 'El programa es ideal para quienes no tienen experiencia formal en gestión de proyectos. Los conceptos del PMBOK se explican de forma muy accesible. Me gustó la dinámica de trabajo en equipos para los casos de estudio.',
                'estado'       => 'aprobado',
                'verificado'   => false,
                'destacada'    => false,
            ],

            
            [
                'programa_id'  => 3,
                'nombre'       => 'Usuario de prueba',
                'cargo_actual' => null,
                'calificacion' => 3,
                'titulo_resena'=> 'Fue aceptable',
                'resena'       => 'El curso está bien pero hay cosas que mejorar. El contenido de tablas dinámicas estuvo bien explicado pero los ejercicios podían ser más variados.',
                'estado'       => 'pendiente',
                'verificado'   => false,
                'destacada'    => false,
            ],
            [
                'programa_id'  => 6,
                'nombre'       => 'Participante anónimo',
                'cargo_actual' => null,
                'calificacion' => 2,
                'titulo_resena'=> 'Esperaba más',
                'resena'       => 'El contenido es bueno pero la organización de los horarios dejó mucho que desear en los primeros módulos. Espero que mejoren para la siguiente cohorte.',
                'estado'       => 'pendiente',
                'verificado'   => false,
                'destacada'    => false,
            ],
        ];

        foreach ($resenas as $r) {
            DB::table('web_programa_resena')->insertOrIgnore(array_merge($r, [
                'ip_origen'  => '127.0.0.1',
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        $total     = DB::table('web_programa_resena')->count();
        $aprobadas = DB::table('web_programa_resena')->where('estado', 'aprobado')->count();
        $destacadas= DB::table('web_programa_resena')->where('destacada', true)->count();
        $pendientes= DB::table('web_programa_resena')->where('estado', 'pendiente')->count();

        $this->command->info("✓ {$total} reseñas insertadas ({$aprobadas} aprobadas, {$destacadas} destacadas, {$pendientes} pendientes).");
    }
}
