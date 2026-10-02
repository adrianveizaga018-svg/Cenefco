<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogArticuloSeeder extends Seeder
{
    public function run(): void
    {
        $articulos = [
            [
                'titulo'               => 'Cómo prepararse para un diplomado en gestión pública',
                'entradilla'           => 'Te compartimos los pasos clave para aprovechar al máximo tu formación en gestión pública y administración del Estado.',
                'contenido'            => "<p>Iniciar un diplomado es una decisión importante. Aquí te presentamos los aspectos fundamentales que debes considerar antes de comenzar.</p>\n\n<h2>1. Define tus objetivos</h2>\n<p>Antes de inscribirte, ten claro qué quieres lograr. ¿Buscar ascender en tu carrera? ¿Cambiar de área? Tener metas claras te mantendrá motivado.</p>\n\n<h2>2. Organiza tu tiempo</h2>\n<p>Un diplomado requiere dedicación. Reserva al menos 8 horas semanales para estudio y actividades del programa.</p>\n\n<h2>3. Participa activamente</h2>\n<p>Las sesiones sincrónicas son una oportunidad única para interactuar con docentes expertos y compañeros de distintas instituciones.</p>",
                'imagen_principal_url' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800',
                'imagen_alt'           => 'Persona estudiando gestión pública',
                'destacada'            => true,
                'fecha_publicacion'    => '2026-03-10 08:00:00',
                'estado_web'           => 'publicado',
                'meta_titulo'          => 'Cómo prepararse para un diplomado en gestión pública | CENEFCO',
                'meta_descripcion'     => 'Descubre los pasos clave para aprovechar al máximo tu formación en gestión pública. Consejos prácticos para estudiantes de diplomados.',
                'etiquetas'            => ['Destacado', 'Con certificado'],
            ],
            [
                'titulo'               => 'La importancia de la formación continua en el sector público',
                'entradilla'           => 'Los servidores públicos que invierten en su formación mejoran la calidad de los servicios que brindan a la ciudadanía.',
                'contenido'            => "<p>En un mundo en constante cambio, la formación continua ya no es una opción sino una necesidad para los profesionales del sector público.</p>\n\n<h2>¿Por qué capacitarse?</h2>\n<p>Las instituciones públicas que cuentan con personal capacitado logran mejores resultados en sus indicadores de gestión y satisfacción ciudadana.</p>\n\n<h2>Modalidades disponibles</h2>\n<p>Hoy existen múltiples modalidades: presencial, virtual y semipresencial, lo que facilita la participación sin descuidar las responsabilidades laborales.</p>",
                'imagen_principal_url' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=800',
                'imagen_alt'           => 'Profesionales en reunión de capacitación',
                'destacada'            => false,
                'fecha_publicacion'    => '2026-03-18 09:30:00',
                'estado_web'           => 'publicado',
                'meta_titulo'          => 'Formación continua en el sector público | CENEFCO',
                'meta_descripcion'     => 'Por qué la capacitación constante es clave para los servidores públicos y cómo impacta en la calidad del servicio ciudadano.',
                'etiquetas'            => ['Presencial', 'Semipresencial'],
            ],
            [
                'titulo'               => 'Nuevas modalidades de aprendizaje en programas de posgrado',
                'entradilla'           => 'La educación virtual y semipresencial abre puertas a profesionales de todo el país, eliminando barreras geográficas.',
                'contenido'            => "<p>La pandemia aceleró la transformación digital en la educación superior, y los programas de posgrado no fueron la excepción.</p>\n\n<h2>Ventajas del aprendizaje híbrido</h2>\n<ul>\n<li>Flexibilidad de horarios</li>\n<li>Acceso desde cualquier lugar del país</li>\n<li>Reducción de costos de desplazamiento</li>\n<li>Materiales disponibles en todo momento</li>\n</ul>\n\n<h2>Herramientas que utilizamos</h2>\n<p>En CENEFCO utilizamos plataformas de videoconferencia, foros de discusión y repositorios digitales para garantizar una experiencia de aprendizaje completa.</p>",
                'imagen_principal_url' => 'https://images.unsplash.com/photo-1588702547919-26089e690ecc?w=800',
                'imagen_alt'           => 'Estudiante en clase virtual',
                'destacada'            => true,
                'fecha_publicacion'    => '2026-04-02 10:00:00',
                'estado_web'           => 'publicado',
                'meta_titulo'          => 'Modalidades de aprendizaje en posgrados | CENEFCO',
                'meta_descripcion'     => 'Conoce las ventajas de los programas híbridos y cómo CENEFCO garantiza calidad educativa en modalidades virtuales y semipresenciales.',
                'etiquetas'            => ['Online', 'Semipresencial', 'Con certificado'],
            ],
            [
                'titulo'               => 'Becas y descuentos disponibles para el segundo semestre 2026',
                'entradilla'           => 'CENEFCO ofrece beneficios económicos para profesionales que deseen iniciar su formación en el segundo semestre del año.',
                'contenido'            => "<p>Con el objetivo de ampliar el acceso a la formación de calidad, CENEFCO lanza su programa de becas y descuentos para el semestre 2026-II.</p>\n\n<h2>Tipos de beneficio</h2>\n<p><strong>Beca completa:</strong> Para los tres mejores postulantes de cada programa, cubriendo el 100% del costo de inscripción.</p>\n<p><strong>Descuento institucional:</strong> 20% de descuento para funcionarios de instituciones públicas con convenio vigente.</p>\n<p><strong>Descuento por pronto pago:</strong> 10% adicional al cancelar el monto total antes del inicio del programa.</p>\n\n<h2>¿Cómo postular?</h2>\n<p>Completa el formulario de preinscripción en nuestro sitio web y selecciona la opción de beca en el formulario. El equipo académico evaluará tu perfil y se comunicará contigo.</p>",
                'imagen_principal_url' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=800',
                'imagen_alt'           => 'Estudiantes recibiendo certificados',
                'destacada'            => true,
                'fecha_publicacion'    => '2026-04-15 08:00:00',
                'estado_web'           => 'publicado',
                'meta_titulo'          => 'Becas y descuentos 2026 | CENEFCO',
                'meta_descripcion'     => 'Conoce los beneficios económicos disponibles para el semestre 2026-II y cómo postular a una beca en CENEFCO.',
                'etiquetas'            => ['Gratuito', 'Cupos limitados', 'Nuevo'],
            ],
            [
                'titulo'               => 'Graduados CENEFCO: historias de éxito en la gestión pública',
                'entradilla'           => 'Profesionales de distintas instituciones comparten cómo la formación recibida transformó su práctica profesional.',
                'contenido'            => "<p>Nada refleja mejor la calidad de un programa educativo que el testimonio de quienes lo vivieron. Presentamos tres historias inspiradoras de graduados de CENEFCO.</p>\n\n<h2>María, jefa de planificación municipal</h2>\n<p>\"El diplomado me dio herramientas concretas para implementar sistemas de monitoreo y evaluación en mi municipio. En seis meses logramos reducir el tiempo de ejecución de proyectos en un 30%.\"</p>\n\n<h2>Roberto, director regional de salud</h2>\n<p>\"La formación en gestión por resultados cambió completamente la forma en que organizo mi equipo. Ahora trabajamos con metas claras y seguimiento semanal.\"</p>\n\n<h2>Ana, coordinadora de recursos humanos</h2>\n<p>\"Gracias al programa de CENEFCO pude diseñar el primer plan de capacitación institucional de nuestra entidad, algo que siempre habíamos postergado.\"</p>",
                'imagen_principal_url' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=800',
                'imagen_alt'           => 'Graduados CENEFCO en ceremonia',
                'destacada'            => false,
                'fecha_publicacion'    => '2026-05-01 09:00:00',
                'estado_web'           => 'publicado',
                'meta_titulo'          => 'Historias de éxito de graduados | CENEFCO',
                'meta_descripcion'     => 'Profesionales comparten cómo los programas de CENEFCO impactaron positivamente en su carrera y en la gestión de sus instituciones.',
                'etiquetas'            => ['Destacado', 'Con certificado'],
            ],
            [
                'titulo'               => 'Calendario académico segundo semestre 2026',
                'entradilla'           => 'Conoce las fechas de inicio, inscripciones y actividades programadas para todos los programas del semestre 2026-II.',
                'contenido'            => "<p>El calendario académico del segundo semestre 2026 ya está disponible. Te presentamos las fechas más importantes.</p>\n\n<h2>Período de inscripciones</h2>\n<p>Del 1 al 30 de junio de 2026. Ingresa al sistema de preinscripción y selecciona el programa de tu interés.</p>\n\n<h2>Inicio de clases</h2>\n<p>Lunes 20 de julio de 2026 para todos los programas del semestre 2026-II.</p>\n\n<h2>Fechas importantes</h2>\n<ul>\n<li>Entrega de trabajos finales: 15 de noviembre de 2026</li>\n<li>Evaluaciones de cierre: 1-5 de diciembre de 2026</li>\n<li>Ceremonia de graduación: 20 de diciembre de 2026</li>\n</ul>",
                'imagen_principal_url' => 'https://images.unsplash.com/photo-1506784365847-bbad939e9335?w=800',
                'imagen_alt'           => 'Calendario académico 2026',
                'destacada'            => false,
                'fecha_publicacion'    => '2026-05-10 08:00:00',
                'estado_web'           => 'publicado',
                'meta_titulo'          => 'Calendario académico 2026-II | CENEFCO',
                'meta_descripcion'     => 'Fechas de inscripciones, inicio de clases y actividades del segundo semestre 2026 en CENEFCO.',
                'etiquetas'            => ['Nuevo', 'Cupos limitados'],
            ],
            [
                'titulo'               => 'Guía de herramientas digitales para el trabajo remoto en el Estado',
                'entradilla'           => 'Un repaso por las plataformas y aplicaciones más útiles para el teletrabajo en instituciones públicas bolivianas.',
                'contenido'            => "<p>El teletrabajo en el sector público requiere no solo disciplina, sino también el dominio de herramientas digitales específicas.</p>\n\n<h2>Herramientas de comunicación</h2>\n<p>Teams, Zoom y Google Meet son las más utilizadas para reuniones virtuales. Dominar sus funciones avanzadas mejora la productividad de los equipos.</p>\n\n<h2>Gestión de documentos</h2>\n<p>Google Workspace y Microsoft 365 permiten colaborar en documentos en tiempo real, reduciendo el intercambio de correos con adjuntos.</p>\n\n<h2>Seguimiento de tareas</h2>\n<p>Trello, Asana o el propio Planner de Microsoft Teams ayudan a organizar el trabajo y hacer seguimiento del avance de proyectos.</p>",
                'imagen_principal_url' => 'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=800',
                'imagen_alt'           => 'Persona trabajando remotamente con laptop',
                'destacada'            => false,
                'fecha_publicacion'    => null,
                'estado_web'           => 'borrador',
                'meta_titulo'          => 'Herramientas digitales para teletrabajo público | CENEFCO',
                'meta_descripcion'     => 'Las mejores plataformas y aplicaciones para el trabajo remoto en instituciones del Estado boliviano.',
                'etiquetas'            => ['Online'],
            ],
            [
                'titulo'               => 'Ética pública: el fundamento de la administración del Estado',
                'entradilla'           => 'La ética no es un componente opcional en la gestión pública; es el pilar sobre el que se construye la confianza ciudadana.',
                'contenido'            => "<p>La ética pública define la forma en que los servidores del Estado ejercen sus funciones y toman decisiones que afectan a miles de personas.</p>\n\n<h2>¿Qué es la ética pública?</h2>\n<p>Es el conjunto de principios y valores que guían el comportamiento de quienes ejercen funciones en nombre del Estado: transparencia, imparcialidad, responsabilidad y honestidad.</p>\n\n<h2>El costo de la falta de ética</h2>\n<p>La corrupción, el nepotismo y el abuso de poder no solo dañan las instituciones, sino que erosionan la confianza de la ciudadanía en el sistema democrático.</p>\n\n<h2>Cómo cultivarla en el día a día</h2>\n<p>Pequeñas decisiones cotidianas, como declarar conflictos de interés o respetar los procedimientos administrativos, construyen una cultura institucional íntegra.</p>",
                'imagen_principal_url' => 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=800',
                'imagen_alt'           => 'Balanza de justicia sobre escritorio',
                'destacada'            => true,
                'fecha_publicacion'    => '2026-02-14 08:00:00',
                'estado_web'           => 'publicado',
                'meta_titulo'          => 'Ética pública en la administración del Estado | CENEFCO',
                'meta_descripcion'     => 'Por qué la ética es el fundamento de la gestión pública y cómo los servidores del Estado pueden cultivarla en su práctica diaria.',
                'etiquetas'            => ['Destacado', 'Presencial'],
            ],
            [
                'titulo'               => 'Planificación estratégica institucional: pasos para implementarla',
                'entradilla'           => 'Una buena planificación estratégica permite a las instituciones públicas anticiparse a los desafíos y maximizar el impacto de sus recursos.',
                'contenido'            => "<p>La planificación estratégica es una herramienta de gestión que permite a las organizaciones definir su rumbo a mediano y largo plazo.</p>\n\n<h2>Paso 1: Diagnóstico situacional</h2>\n<p>Antes de planificar, es necesario conocer la situación actual de la institución. El análisis FODA es una metodología ampliamente utilizada para este propósito.</p>\n\n<h2>Paso 2: Definición de la misión y visión</h2>\n<p>¿Para qué existe la institución? ¿A dónde quiere llegar? Estas preguntas guían la formulación de la misión y la visión institucional.</p>\n\n<h2>Paso 3: Formulación de objetivos estratégicos</h2>\n<p>Los objetivos deben ser SMART: específicos, medibles, alcanzables, relevantes y con plazo definido.</p>\n\n<h2>Paso 4: Seguimiento y evaluación</h2>\n<p>Un plan sin seguimiento es solo un documento. Establece indicadores y mecanismos de reporte periódico.</p>",
                'imagen_principal_url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800',
                'imagen_alt'           => 'Gráficos de planificación estratégica en pizarrón',
                'destacada'            => false,
                'fecha_publicacion'    => '2026-02-28 09:00:00',
                'estado_web'           => 'publicado',
                'meta_titulo'          => 'Planificación estratégica institucional paso a paso | CENEFCO',
                'meta_descripcion'     => 'Guía práctica para implementar un proceso de planificación estratégica en instituciones públicas bolivianas.',
                'etiquetas'            => ['Con certificado', 'Presencial'],
            ],
            [
                'titulo'               => 'Gestión por resultados: cómo medir el impacto de las políticas públicas',
                'entradilla'           => 'El enfoque de gestión por resultados transforma la manera en que las instituciones diseñan, ejecutan y evalúan sus intervenciones.',
                'contenido'            => "<p>La gestión por resultados (GpR) es un modelo de administración pública que orienta todos los recursos y acciones hacia el logro de resultados con valor público.</p>\n\n<h2>¿Por qué es importante?</h2>\n<p>Permite pasar de una gestión centrada en procesos a una centrada en impactos reales en la vida de las personas.</p>\n\n<h2>Indicadores clave</h2>\n<p>Un buen sistema de GpR requiere indicadores de producto, resultado e impacto, cada uno con su línea base y meta definida.</p>\n\n<h2>Casos de éxito en Bolivia</h2>\n<p>Varias entidades territoriales autónomas han implementado con éxito modelos de GpR vinculados a sus planes de desarrollo, obteniendo mejoras significativas en la ejecución presupuestaria.</p>",
                'imagen_principal_url' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800',
                'imagen_alt'           => 'Dashboard con indicadores de gestión',
                'destacada'            => false,
                'fecha_publicacion'    => '2026-03-05 10:00:00',
                'estado_web'           => 'publicado',
                'meta_titulo'          => 'Gestión por resultados en políticas públicas | CENEFCO',
                'meta_descripcion'     => 'Cómo medir el impacto real de las políticas públicas mediante el enfoque de gestión por resultados.',
                'etiquetas'            => ['Online', 'Con certificado'],
            ],
            [
                'titulo'               => 'El papel del liderazgo en la transformación institucional',
                'entradilla'           => 'Los líderes públicos tienen la responsabilidad de impulsar cambios organizacionales que mejoren la eficiencia y el servicio a los ciudadanos.',
                'contenido'            => "<p>El liderazgo en el sector público va más allá de ocupar un cargo directivo. Implica inspirar equipos, gestionar el cambio y mantener el foco en el bienestar ciudadano.</p>\n\n<h2>Liderazgo transformacional</h2>\n<p>Este estilo de liderazgo motiva a los colaboradores a superar sus propios intereses individuales en beneficio del propósito organizacional.</p>\n\n<h2>Competencias del líder público</h2>\n<ul>\n<li>Pensamiento estratégico</li>\n<li>Comunicación efectiva</li>\n<li>Gestión del conflicto</li>\n<li>Orientación a resultados</li>\n<li>Inteligencia emocional</li>\n</ul>\n\n<h2>El liderazgo se aprende</h2>\n<p>Las competencias de liderazgo pueden desarrollarse mediante formación especializada, mentoría y práctica reflexiva. Los programas de CENEFCO incorporan metodologías activas que potencian estas habilidades.</p>",
                'imagen_principal_url' => 'https://images.unsplash.com/photo-1521791136064-7986c2920216?w=800',
                'imagen_alt'           => 'Líder hablando frente a su equipo',
                'destacada'            => true,
                'fecha_publicacion'    => '2026-03-25 08:30:00',
                'estado_web'           => 'publicado',
                'meta_titulo'          => 'Liderazgo en la transformación institucional pública | CENEFCO',
                'meta_descripcion'     => 'Cómo el liderazgo efectivo impulsa la transformación en las instituciones públicas y qué competencias debe desarrollar un líder público.',
                'etiquetas'            => ['Destacado', 'Semipresencial'],
            ],
            [
                'titulo'               => 'Presupuesto por programas: guía para servidores públicos',
                'entradilla'           => 'Entender la estructura presupuestaria es clave para una gestión eficiente de los recursos públicos.',
                'contenido'            => "<p>El presupuesto por programas es la metodología oficial de programación presupuestaria en Bolivia, que vincula los recursos financieros con los resultados esperados de cada entidad.</p>\n\n<h2>Estructura básica</h2>\n<p>El presupuesto se organiza en programas, actividades y proyectos, cada uno con asignaciones presupuestarias específicas y responsables definidos.</p>\n\n<h2>Clasificadores presupuestarios</h2>\n<p>Los clasificadores organizan el gasto por su naturaleza (corriente, de capital), por objeto del gasto (personal, bienes, servicios) y por fuente de financiamiento.</p>\n\n<h2>Proceso presupuestario</h2>\n<p>El ciclo presupuestario boliviano comprende: formulación (junio-agosto), aprobación (septiembre-noviembre), ejecución (enero-diciembre) y evaluación (permanente).</p>",
                'imagen_principal_url' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800',
                'imagen_alt'           => 'Documentos de presupuesto y calculadora',
                'destacada'            => false,
                'fecha_publicacion'    => '2026-04-08 09:00:00',
                'estado_web'           => 'publicado',
                'meta_titulo'          => 'Presupuesto por programas para servidores públicos | CENEFCO',
                'meta_descripcion'     => 'Guía práctica sobre la estructura y el proceso presupuestario boliviano para servidores públicos.',
                'etiquetas'            => ['Presencial', 'Con certificado'],
            ],
            [
                'titulo'               => 'Contratación pública en Bolivia: errores comunes y cómo evitarlos',
                'entradilla'           => 'Los procesos de contratación pública son críticos para la ejecución presupuestaria. Conocer los errores más frecuentes ayuda a prevenirlos.',
                'contenido'            => "<p>La contratación pública representa uno de los procesos más sensibles de la gestión institucional, tanto por los montos involucrados como por su exposición a riesgos de corrupción.</p>\n\n<h2>Error 1: Especificaciones técnicas deficientes</h2>\n<p>Términos de referencia o especificaciones técnicas mal redactadas generan impugnaciones, demoras y en algunos casos la declaratoria desierta del proceso.</p>\n\n<h2>Error 2: Incumplimiento de plazos legales</h2>\n<p>La Ley 1178 y el SICOES establecen plazos precisos. Incumplirlos puede anular el proceso y generar responsabilidades para los servidores involucrados.</p>\n\n<h2>Error 3: Fraccionamiento indebido</h2>\n<p>Dividir contrataciones para eludir modalidades de mayor control es una práctica prohibida que puede derivar en procesos administrativos.</p>\n\n<h2>Buenas prácticas</h2>\n<p>Planificar con anticipación, documentar adecuadamente cada etapa y capacitar al equipo responsable son las mejores medidas preventivas.</p>",
                'imagen_principal_url' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=800',
                'imagen_alt'           => 'Firma de contrato público',
                'destacada'            => false,
                'fecha_publicacion'    => '2026-04-22 10:00:00',
                'estado_web'           => 'publicado',
                'meta_titulo'          => 'Errores en contratación pública Bolivia | CENEFCO',
                'meta_descripcion'     => 'Los errores más comunes en los procesos de contratación pública en Bolivia y cómo prevenirlos.',
                'etiquetas'            => ['Presencial', 'Cupos limitados'],
            ],
            [
                'titulo'               => 'Transparencia y acceso a la información: derechos ciudadanos',
                'entradilla'           => 'Toda persona tiene derecho a acceder a la información que producen y manejan las entidades públicas. ¿Cómo ejercer este derecho?',
                'contenido'            => "<p>El derecho de acceso a la información pública es un derecho fundamental reconocido en la Constitución Política del Estado boliviano.</p>\n\n<h2>¿Qué información es pública?</h2>\n<p>Toda la información producida, obtenida o bajo control de las entidades del Estado es pública por defecto, salvo las excepciones expresamente previstas en la ley.</p>\n\n<h2>¿Cómo solicitar información?</h2>\n<p>Las solicitudes deben presentarse por escrito ante la Unidad de Transparencia de la entidad correspondiente. El plazo de respuesta es de 20 días hábiles.</p>\n\n<h2>Obligaciones de las entidades</h2>\n<p>Las entidades deben publicar de oficio en sus sitios web la información mínima establecida por normativa: presupuesto, contratos, declaraciones juradas, entre otros.</p>",
                'imagen_principal_url' => 'https://images.unsplash.com/photo-1532619675605-1ede6c2ed2b0?w=800',
                'imagen_alt'           => 'Manos sosteniendo hoja con datos públicos',
                'destacada'            => false,
                'fecha_publicacion'    => '2026-05-05 08:00:00',
                'estado_web'           => 'publicado',
                'meta_titulo'          => 'Transparencia y acceso a información pública en Bolivia | CENEFCO',
                'meta_descripcion'     => 'Cómo ejercer el derecho de acceso a la información pública y cuáles son las obligaciones de las entidades del Estado.',
                'etiquetas'            => ['Online', 'Gratuito'],
            ],
            [
                'titulo'               => 'Evaluación del desempeño: cómo implementarla en instituciones públicas',
                'entradilla'           => 'Un sistema de evaluación del desempeño bien diseñado mejora la productividad, motiva al personal y permite identificar necesidades de capacitación.',
                'contenido'            => "<p>La evaluación del desempeño es una herramienta de gestión de recursos humanos que permite medir la contribución de cada servidor al logro de los objetivos institucionales.</p>\n\n<h2>Principios de una buena evaluación</h2>\n<p>Debe ser objetiva, basada en evidencias, participativa y vinculada a los resultados esperados del cargo.</p>\n\n<h2>Métodos más utilizados</h2>\n<p>La evaluación por objetivos (MBO), la evaluación 360° y las rúbricas de competencias son los métodos más aplicados en el sector público latinoamericano.</p>\n\n<h2>Errores a evitar</h2>\n<p>El efecto halo, la tendencia central y el sesgo de recencia son los errores más comunes que comprometen la objetividad del proceso evaluativo.</p>\n\n<h2>Vinculación con la capacitación</h2>\n<p>Los resultados de la evaluación deben alimentar el plan de capacitación institucional, cerrando las brechas identificadas.</p>",
                'imagen_principal_url' => 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=800',
                'imagen_alt'           => 'Reunión de evaluación de desempeño',
                'destacada'            => false,
                'fecha_publicacion'    => '2026-05-12 09:30:00',
                'estado_web'           => 'publicado',
                'meta_titulo'          => 'Evaluación del desempeño en instituciones públicas | CENEFCO',
                'meta_descripcion'     => 'Guía para implementar un sistema de evaluación del desempeño efectivo en entidades del sector público boliviano.',
                'etiquetas'            => ['Semipresencial', 'Con certificado'],
            ],
            [
                'titulo'               => 'Innovación en el sector público: casos bolivianos',
                'entradilla'           => 'Varias instituciones públicas bolivianas están liderando procesos de innovación que mejoran la entrega de servicios a la ciudadanía.',
                'contenido'            => "<p>La innovación pública no requiere grandes presupuestos; requiere voluntad, creatividad y un enfoque centrado en el ciudadano.</p>\n\n<h2>¿Qué es la innovación pública?</h2>\n<p>Es la introducción de nuevas ideas, procesos, productos o formas de organización en las entidades del Estado para generar valor público.</p>\n\n<h2>Caso 1: Ventanilla única digital</h2>\n<p>Un gobierno municipal implementó una plataforma digital que permite a los ciudadanos realizar más de 30 trámites en línea, reduciendo las colas en un 70%.</p>\n\n<h2>Caso 2: Laboratorio de innovación ciudadana</h2>\n<p>Una gobernación departamental creó un espacio de co-creación donde funcionarios y ciudadanos diseñan juntos soluciones a problemas locales.</p>\n\n<h2>¿Cómo empezar?</h2>\n<p>Identifica un problema concreto, involucra a los usuarios del servicio, prototipa soluciones a pequeña escala y aprende de los errores.</p>",
                'imagen_principal_url' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=800',
                'imagen_alt'           => 'Equipo de trabajo colaborando con post-its',
                'destacada'            => true,
                'fecha_publicacion'    => null,
                'estado_web'           => 'borrador',
                'meta_titulo'          => 'Innovación pública: casos bolivianos | CENEFCO',
                'meta_descripcion'     => 'Ejemplos reales de innovación en instituciones públicas bolivianas y cómo aplicar el pensamiento innovador en tu entidad.',
                'etiquetas'            => ['Nuevo', 'Online'],
            ],
        ];

        $etiquetaIds = DB::table('web_etiqueta')->pluck('id', 'nombre');
        $nextId      = (DB::table('t_articulo')->max('id_art') ?? 0) + 1;

        foreach ($articulos as $data) {
            $etiquetasNombres = $data['etiquetas'];
            unset($data['etiquetas']);

            $slug = Str::slug($data['titulo']);

            if (DB::table('t_articulo')->where('slug', $slug)->exists()) {
                continue;
            }

            $id = $nextId++;

            DB::table('t_articulo')->insertOrIgnore([
                'id_art'               => $id,
                'id_us_reg'            => 0,
                'num_art'              => 0,
                'titulo'               => $data['titulo'],
                'slug'                 => $slug,
                'entradilla'           => $data['entradilla'],
                'contenido'            => $data['contenido'],
                'imagen_principal_url' => $data['imagen_principal_url'],
                'imagen_alt'           => $data['imagen_alt'],
                'destacada'            => $data['destacada'] ? 1 : 0,
                'fecha_publicacion'    => $data['fecha_publicacion'],
                'estado_web'           => $data['estado_web'],
                'meta_titulo'          => $data['meta_titulo'],
                'meta_descripcion'     => $data['meta_descripcion'],
                'estado'               => 1,
                'vistas'               => rand(10, 350),
                'fecha_reg'            => now()->toDateTimeString(),
                'updated_at'           => now()->toDateTimeString(),
            ]);

            $pivotRows = [];
            foreach ($etiquetasNombres as $nombre) {
                if (isset($etiquetaIds[$nombre])) {
                    $pivotRows[] = ['articulo_id' => $id, 'etiqueta_id' => $etiquetaIds[$nombre]];
                }
            }
            if ($pivotRows) {
                DB::table('web_articulo_etiqueta')->insertOrIgnore($pivotRows);
            }
        }
    }
}
