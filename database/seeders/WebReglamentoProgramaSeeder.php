<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class WebReglamentoProgramaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $registros = [
            
            [
                'id_programa' => 1,
                'bienvenida' => "Es un honor para el Centro Nacional de Educación y Formación Continua — CENEFCO darle la más cordial bienvenida al {programa}. Este diplomado está diseñado para fortalecer sus capacidades en la administración y gestión de organismos públicos, con un enfoque práctico orientado a la transformación del servicio estatal. Estamos convencidos de que su participación enriquecerá el espacio académico y contribuirá al desarrollo de una gestión pública más eficiente y transparente.",
                'reglas_asistencia' => "La asistencia mínima requerida para aprobar el diplomado es del 80% de las sesiones programadas.\nEl ingreso tardío (más de 15 minutos) contará como media asistencia.\nLas inasistencias justificadas deben comunicarse al coordinador con anticipación mediante WhatsApp o correo.\nTres inasistencias consecutivas sin justificación serán reportadas a coordinación académica.\nLas sesiones de evaluación y defensa de proyectos tienen asistencia obligatoria al 100%.",
                'reglas_evaluacion' => "El diplomado se evalúa mediante trabajos prácticos (40%), examen parcial (30%) y proyecto final de gestión pública (30%).\nLos trabajos deben entregarse antes de la fecha límite indicada por el docente.\nLos proyectos finales deben desarrollarse en grupos de máximo 3 personas.\nLa nota mínima de aprobación es 51/100.\nLos trabajos entregados fuera de plazo tendrán una penalización del 20% de la nota obtenida.",
                'reglas_pagos' => "El pago de la primera cuota debe realizarse antes del inicio del diplomado.\nLas cuotas siguientes tienen fecha límite el día 10 de cada mes.\nPasados 15 días de mora, el estudiante no podrá ingresar a clases hasta regularizar su situación.\nLos pagos se realizan mediante transferencia bancaria o depósito; enviar comprobante al número de coordinación.\nNo se realizan devoluciones una vez iniciado el programa, salvo casos de fuerza mayor debidamente documentados.",
                'reglas_conducta' => "Se espera un trato respetuoso y profesional entre estudiantes, docentes y personal administrativo.\nEstá prohibido el uso de teléfonos celulares durante las exposiciones y exámenes.\nCualquier forma de discriminación, acoso o violencia será sancionada con la baja inmediata del programa.\nSe debe mantener un ambiente de debate académico fundamentado en argumentos, no en descalificaciones.",
                'reglas_plataformas' => "Las sesiones virtuales se realizan vía Zoom; el enlace se comparte con 24 horas de anticipación por WhatsApp.\nActivar la cámara durante las sesiones en vivo es obligatorio salvo problemas técnicos justificados.\nEl grupo de WhatsApp es exclusivo para comunicados académicos y coordinación.\nNo está permitido grabar ni redistribuir las sesiones sin autorización expresa del docente y CENEFCO.",
                'reglas_derechos' => "Recibir una formación de calidad impartida por docentes certificados con experiencia en el sector público.\nAcceder a los materiales de estudio y bibliografía recomendada durante toda la duración del diplomado.\nSolicitar aclaraciones y retroalimentación sobre sus evaluaciones en un plazo de 72 horas.\nRecibir su certificado de aprobación o participación al concluir el programa, según corresponda.",
            ],

            
            [
                'id_programa' => 2,
                'bienvenida' => "Es un honor para el Centro Nacional de Educación y Formación Continua — CENEFCO darle la más cordial bienvenida al {programa}. Este diplomado le brindará las herramientas jurídicas fundamentales para comprender el marco legal que rige la actividad empresarial en Bolivia: constitución de empresas, contratos, relaciones laborales y resolución de conflictos comerciales. Le invitamos a construir una sólida base legal que respalde su ejercicio profesional.",
                'reglas_asistencia' => "La modalidad es semipresencial; se requiere el 80% de asistencia a sesiones presenciales y el 100% de acceso a módulos virtuales.\nLas sesiones presenciales se realizan según el calendario académico publicado.\nLas ausencias a sesiones presenciales deben justificarse con 48 horas de antelación.\nLos módulos virtuales tienen fecha límite de acceso y revisión que no se extiende.\nTres inasistencias presenciales injustificadas habilitan al docente a inhabilitar al estudiante.",
                'reglas_evaluacion' => "Evaluación: módulos virtuales (20%), participación en clase (20%), análisis jurídico escrito (30%), examen final (30%).\nEl análisis jurídico debe presentarse sobre un caso empresarial real o ficticio de Bolivia.\nEl examen final es presencial y abarca todos los módulos del diplomado.\nNota mínima de aprobación: 51/100.\nLos trabajos con plagio serán anulados; segunda infracción implica expulsión del programa.",
                'reglas_pagos' => "El pago íntegro o primera cuota es obligatorio para garantizar su matrícula.\nCuotas siguientes con vencimiento el día 10 de cada mes.\nMora de más de 20 días restringe el acceso a plataforma y sesiones presenciales.\nPagos aceptados: transferencia bancaria, depósito o pago QR.\nNo se realizan reembolsos una vez iniciado el segundo módulo.",
                'reglas_conducta' => "El rigor y la ética son pilares del Derecho; se espera esa misma actitud en el aula.\nLas discusiones jurídicas deben fundamentarse en legislación boliviana y doctrina.\nRespetar la confidencialidad de los casos empresariales discutidos en clase.\nNo está permitido el uso de celulares durante las exposiciones y exámenes presenciales.",
                'reglas_plataformas' => "Los módulos virtuales están disponibles en la plataforma Moodle de CENEFCO.\nLas sesiones en vivo se realizan por Zoom cuando la modalidad lo requiera; enlace por WhatsApp.\nEl grupo de WhatsApp del curso es solo para comunicados académicos y coordinación.\nNo grabar ni redistribuir contenido sin autorización del docente.",
                'reglas_derechos' => "Recibir instrucción de derecho empresarial actualizada a la legislación boliviana vigente.\nAcceder a biblioteca digital con normativa, jurisprudencia y manuales jurídicos durante el programa.\nSolicitar aclaración sobre calificaciones en un plazo de 5 días hábiles.\nRecibir su certificado al aprobar el programa.",
            ],

            
            [
                'id_programa' => 3,
                'bienvenida' => "Es un honor para el Centro Nacional de Educación y Formación Continua — CENEFCO darle la más cordial bienvenida al {programa}. En este curso aprenderá a dominar las herramientas más potentes de Microsoft Excel para análisis financiero, automatización de procesos y toma de decisiones empresariales. Con práctica intensiva y casos reales del mundo de los negocios, potenciará su perfil profesional de manera inmediata.",
                'reglas_asistencia' => "Se requiere un mínimo del 80% de asistencia a las sesiones prácticas del curso.\nEl ingreso tardío (más de 10 minutos) contará como media asistencia.\nLas inasistencias deben notificarse al coordinador antes de la sesión por WhatsApp.\nLas prácticas de laboratorio en computadora son de asistencia obligatoria.",
                'reglas_evaluacion' => "Evaluación: ejercicios prácticos semanales (40%), proyecto de automatización (35%), evaluación final práctica (25%).\nTodos los ejercicios deben entregarse en los archivos Excel indicados por el docente.\nEl proyecto final debe resolver un caso real de gestión financiera o empresarial.\nNota mínima de aprobación: 51/100.",
                'reglas_pagos' => "La primera cuota debe abonarse antes del inicio del curso.\nCuotas siguientes con fecha límite el día 5 de cada mes.\nEl no pago en plazo puede suspender el acceso a la plataforma y materiales.\nSe aceptan pagos por QR, transferencia o depósito.",
                'reglas_conducta' => "Traer o tener acceso a computadora con Microsoft Excel instalado en cada sesión práctica.\nRespetar los turnos de participación y las consultas de los compañeros.\nNo usar las computadoras del aula para fines ajenos al curso durante las clases.\nMantener el ambiente de aprendizaje colaborativo.",
                'reglas_plataformas' => "Los materiales, plantillas y archivos de práctica se publican en la plataforma Moodle antes de cada sesión.\nLas sesiones virtuales se transmiten por Zoom con acceso a pantalla del docente.\nEl grupo de WhatsApp es para dudas académicas y coordinación.\nNo redistribuir los archivos del curso fuera del grupo.",
                'reglas_derechos' => "Acceder a todos los archivos de práctica, plantillas y recursos del curso.\nRecibir retroalimentación personalizada sobre sus proyectos y ejercicios.\nObtener su certificado de aprobación o participación al finalizar el programa.\nSer asistido en dudas técnicas sobre el uso de Excel durante el curso.",
            ],

            
            [
                'id_programa' => 4,
                'bienvenida' => "Es un honor para el Centro Nacional de Educación y Formación Continua — CENEFCO darle la más cordial bienvenida al {programa}. Este curso está diseñado especialmente para emprendedores y pequeños empresarios que desean comprender la contabilidad como herramienta de gestión de su negocio. Con un enfoque práctico y sin tecnicismos innecesarios, aprenderá a llevar sus registros, interpretar estados financieros y tomar mejores decisiones.",
                'reglas_asistencia' => "La asistencia mínima requerida es del 80% de las sesiones del curso.\nEl ingreso tardío (más de 10 minutos) contará como media asistencia.\nLas inasistencias deben justificarse por WhatsApp antes de la sesión.\nMás de 3 faltas injustificadas implican la reprobación del curso.",
                'reglas_evaluacion' => "Evaluación: participación activa (20%), ejercicios de registro contable (40%), caso práctico final (40%).\nEl caso práctico final consiste en llevar la contabilidad básica de un negocio real o simulado.\nNota mínima de aprobación: 51/100.\nSe permite una recuperación al finalizar el curso.",
                'reglas_pagos' => "El pago total o primera cuota debe realizarse antes del inicio del curso.\nCuotas siguientes con vencimiento el día 5 de cada mes.\nEl atraso en el pago puede limitar el acceso a materiales.\nSe aceptan todos los medios de pago habilitados por CENEFCO.",
                'reglas_conducta' => "Mantener un ambiente de aprendizaje respetuoso y colaborativo.\nTraer materiales de trabajo (calculadora, cuaderno o laptop) a cada sesión.\nLas dudas y consultas son bienvenidas; se fomenta la participación activa.\nEvitar el uso del celular con fines ajenos al curso durante las clases.",
                'reglas_plataformas' => "Los materiales y formatos contables se distribuyen en la plataforma Moodle o por WhatsApp.\nLas sesiones virtuales se realizan por Zoom; enlace disponible en el grupo del curso.\nEl grupo de WhatsApp es exclusivo para temas académicos del programa.",
                'reglas_derechos' => "Acceder a todos los materiales, formatos y guías del curso.\nRecibir orientación personalizada sobre los ejercicios y el caso práctico.\nObtener su certificado de aprobación o participación al concluir.\nSer tratado con respeto y recibir respuesta a sus consultas en tiempo razonable.",
            ],

            
            [
                'id_programa' => 5,
                'bienvenida' => "Es un honor para el Centro Nacional de Educación y Formación Continua — CENEFCO darle la más cordial bienvenida al {programa}. Este taller intensivo le ayudará a superar el miedo a hablar en público, desarrollar confianza en sus presentaciones y comunicarse con claridad y persuasión en entornos profesionales. Prepárese para transformar su comunicación y potenciar su impacto personal y profesional.",
                'reglas_asistencia' => "Dado el carácter práctico del taller, la asistencia al 90% de las sesiones es obligatoria para recibir certificado.\nEl taller requiere participación activa; la presencia pasiva no cuenta como asistencia válida.\nSolo se permiten 1 inasistencia justificada durante todo el programa.\nLas sesiones de presentación pública son de asistencia y participación obligatoria.",
                'reglas_evaluacion' => "Evaluación: ejercicios de práctica en cada sesión (30%), presentación individual (40%), autopercepción y mejora (30%).\nTodas las evaluaciones son prácticas; no hay examen escrito.\nLa nota mínima de aprobación es 51/100.\nSe valoran especialmente el progreso y la actitud durante el taller.",
                'reglas_pagos' => "El pago debe realizarse antes del inicio del taller.\nEl taller es de pago único; no hay cuotas.\nNo se realizan reembolsos una vez iniciado el programa.\nSe aceptan transferencia, depósito o QR.",
                'reglas_conducta' => "El respeto mutuo es fundamental: cada participante está en proceso de mejora.\nCelebrar los avances de los compañeros y evitar críticas destructivas.\nComprometerse con las prácticas en clase y las actividades fuera del aula.\nEl celular debe estar en modo silencio durante las presentaciones.",
                'reglas_plataformas' => "Los materiales y recursos de práctica se comparten por WhatsApp y Moodle.\nLas sesiones virtuales (si aplica) se realizan por Zoom con cámara activada obligatoriamente.\nEl grupo de WhatsApp es para coordinación y práctica extra-curricular.",
                'reglas_derechos' => "Recibir retroalimentación constructiva y personalizada en cada sesión.\nAcceder a recursos de práctica adicionales y grabaciones de referencia.\nObtener su certificado de participación o aprobación al concluir el taller.\nSer tratado con respeto y aliento durante todo el proceso de aprendizaje.",
            ],

            
            [
                'id_programa' => 6,
                'bienvenida' => "Es un honor para el Centro Nacional de Educación y Formación Continua — CENEFCO darle la más cordial bienvenida a la {programa}. Este programa de posgrado de alto nivel le preparará para liderar organizaciones con visión estratégica, dominar las finanzas empresariales, gestionar equipos de alto rendimiento y tomar decisiones basadas en datos. Estamos comprometidos con su desarrollo integral como ejecutivo y líder empresarial.",
                'reglas_asistencia' => "La asistencia mínima requerida para aprobar cada módulo es del 80% de las sesiones.\nEl ingreso tardío (más de 15 minutos) contará como media asistencia.\nLas inasistencias justificadas deben comunicarse con mínimo 24 horas de anticipación.\nLas sesiones de presentación de proyectos son de asistencia obligatoria al 100%.\nTres inasistencias consecutivas sin justificación serán reportadas al Comité Académico.",
                'reglas_evaluacion' => "Evaluación por módulo: participación (20%), trabajos individuales (30%), proyecto grupal (20%), examen final (30%).\nEl proyecto de tesis se desarrolla a lo largo del programa y se defiende ante un tribunal académico.\nLos proyectos grupales se realizan en equipos de máximo 4 personas.\nLa nota mínima de aprobación es 61/100 (estándar de posgrado).\nNo se acepta plagio; el incumplimiento puede resultar en la expulsión del programa.",
                'reglas_pagos' => "El pago de la primera cuota es requisito de matrícula definitiva.\nLas cuotas mensuales tienen fecha límite el día 10 de cada mes.\nMora de más de 15 días restringe el acceso a clases y plataforma.\nSe ofrecen planes de financiamiento especiales; consultar con administración.\nNo se emite diploma hasta la cancelación total del programa.",
                'reglas_conducta' => "Se espera el más alto nivel de profesionalismo y ética en todas las interacciones.\nLa puntualidad es un valor fundamental; el MBA exige disciplina y compromiso.\nLas discusiones deben ser constructivas y basadas en evidencia y datos.\nRespetar la confidencialidad de los casos empresariales reales que se analicen en clase.\nCualquier conducta contraria a los valores del MBA será tratada por el Comité Académico.",
                'reglas_plataformas' => "Las sesiones virtuales se realizan por Zoom con cámara activada obligatoriamente.\nLa plataforma Moodle concentra todos los materiales, casos y evaluaciones del programa.\nEl grupo de WhatsApp es exclusivo para coordinación y comunicados académicos oficiales.\nNo está permitido grabar ni redistribuir sesiones sin autorización expresa del docente.",
                'reglas_derechos' => "Recibir formación de excelencia con docentes con experiencia directiva y académica comprobada.\nAcceder a biblioteca digital, casos Harvard y recursos premium durante todo el MBA.\nSolicitar tutoría académica personalizada en cualquier módulo del programa.\nRecibir su diploma de MBA al concluir y aprobar el programa.\nSer tratado con respeto y dignidad en todo momento por la comunidad académica del programa.",
            ],

            
            [
                'id_programa' => 7,
                'bienvenida' => "Es un honor para el Centro Nacional de Educación y Formación Continua — CENEFCO darle la más cordial bienvenida a la {programa}. Este programa le dotará de competencias clave para diseñar, ejecutar y medir estrategias digitales: SEO, SEM, redes sociales, email marketing, e-commerce y analítica web. Con herramientas reales y casos prácticos, estará listo para liderar la presencia digital de cualquier organización.",
                'reglas_asistencia' => "La asistencia mínima a sesiones en vivo es del 75%.\nEl enlace de Zoom se comparte 30 minutos antes de cada sesión en el grupo de WhatsApp.\nLas sesiones quedan grabadas y disponibles por 72 horas para quienes no pudieron conectarse.\nLas ausencias a sesiones evaluadas (talleres en vivo) no se recuperan con la grabación.\nMás de 4 ausencias a sesiones en vivo sin justificación resultan en inhabilitación.",
                'reglas_evaluacion' => "Evaluación: ejercicios semanales (30%), campaña digital grupal (40%), portafolio final (30%).\nLos ejercicios semanales deben entregarse dentro de las 48 horas posteriores a cada sesión.\nLa campaña digital se desarrolla en grupos de 2 a 3 personas sobre un negocio real o simulado.\nEl portafolio final incluye todas las piezas creadas durante el programa.\nNota mínima de aprobación: 51/100.",
                'reglas_pagos' => "La primera cuota debe pagarse antes de la sesión inaugural.\nCuotas mensuales con vencimiento el día 5 de cada mes.\nLa mora suspende temporalmente el acceso a la plataforma y grabaciones.\nSe aceptan pagos por QR, transferencia o depósito.",
                'reglas_conducta' => "Activar la cámara durante las sesiones en vivo es obligatorio salvo problemas técnicos.\nMantener el micrófono en silencio cuando no se esté participando activamente.\nRespetar los proyectos digitales de los compañeros; la creatividad merece un espacio seguro.\nNo usar el espacio del programa para promocionar productos o servicios externos.",
                'reglas_plataformas' => "Todas las sesiones se realizan por Zoom; es obligatorio tener la aplicación actualizada.\nLos materiales, plantillas y recursos se publican en la plataforma Moodle antes de cada sesión.\nEl grupo de WhatsApp del programa es para comunicados académicos y coordinación.\nLas grabaciones de las sesiones son de uso exclusivo de los estudiantes matriculados.",
                'reglas_derechos' => "Recibir formación práctica con herramientas digitales reales (Meta Ads, Google Ads, Canva, etc.).\nAcceder a grabaciones de sesiones por 72 horas en caso de inconvenientes técnicos.\nRecibir retroalimentación personalizada sobre su campaña y portafolio digital.\nObtener su certificado de aprobación o participación al finalizar el programa.",
            ],

            
            [
                'id_programa' => 8,
                'bienvenida' => "Es un honor para el Centro Nacional de Educación y Formación Continua — CENEFCO darle la más cordial bienvenida al {programa}. Este seminario de actualización le mantendrá al día sobre las últimas modificaciones en la normativa tributaria boliviana: NIT, facturación electrónica, declaraciones juradas e IVA. Diseñado para contadores, administradores y emprendedores que necesitan operar en estricto cumplimiento de la ley.",
                'reglas_asistencia' => "El seminario es intensivo; se requiere asistencia al 90% de las sesiones para recibir certificado.\nEl ingreso tardío (más de 10 minutos) no será permitido en módulos de evaluación.\nLas inasistencias deben justificarse con anticipación por WhatsApp al coordinador.\nLa asistencia a la sesión de casos prácticos es obligatoria.",
                'reglas_evaluacion' => "Evaluación: cuestionario por módulo (50%), caso práctico tributario (50%).\nEl caso práctico requiere resolver una situación tributaria real de una empresa boliviana.\nNota mínima de aprobación: 51/100.\nNo se permite el uso de materiales externos durante los cuestionarios.",
                'reglas_pagos' => "El pago del seminario es único y debe realizarse antes del inicio.\nNo se realizan reembolsos una vez iniciado el primer módulo.\nSe aceptan transferencia, depósito o QR.\nContactar a administración para coordinar el comprobante de pago.",
                'reglas_conducta' => "Traer o tener acceso digital a la normativa tributaria boliviana vigente.\nLas consultas y casos prácticos son bienvenidos; fomentan el aprendizaje colectivo.\nMantener confidencialidad sobre los casos reales de empresas que se mencionen.\nEl uso del celular durante los módulos de evaluación está expresamente prohibido.",
                'reglas_plataformas' => "Los materiales normativos y guías se distribuyen por Moodle y WhatsApp del curso.\nLas sesiones virtuales se realizan por Zoom; enlace disponible 24h antes por WhatsApp.\nEl grupo de WhatsApp es para coordinación académica y resolución de dudas tributarias.",
                'reglas_derechos' => "Recibir formación tributaria actualizada a la normativa 2026 del SIN Bolivia.\nAcceder a toda la normativa, formularios y guías durante el seminario.\nObtener su certificado de participación o aprobación al concluir.\nRealizar consultas técnicas al docente sobre casos tributarios durante el seminario.",
            ],

            
            [
                'id_programa' => 9,
                'bienvenida' => "Es un honor para el Centro Nacional de Educación y Formación Continua — CENEFCO darle la más cordial bienvenida al {programa}. Este programa de certificación intensiva le prepara para comprender y aplicar el estándar del Project Management Institute (PMI) según el PMBOK®, con orientación a la gestión eficiente de proyectos en organizaciones públicas y privadas. Al concluir, contará con los fundamentos necesarios para avanzar hacia la certificación PMP® o CAPM®.",
                'reglas_asistencia' => "Dada la modalidad intensiva, la asistencia al 90% de las sesiones es obligatoria para recibir certificado.\nLas sesiones tienen duración extendida; el ingreso tardío de más de 20 minutos no será admitido.\nSolo se permiten 2 inasistencias justificadas durante todo el programa.\nLas sesiones de simulacro de examen PMI son de asistencia y participación obligatoria al 100%.",
                'reglas_evaluacion' => "Evaluación: cuestionarios por área de conocimiento (40%), caso práctico de proyecto (35%), simulacro final PMI (25%).\nLos cuestionarios se realizan al finalizar cada área de conocimiento del PMBOK®.\nEl caso práctico requiere aplicar los grupos de procesos PMI a un proyecto documentado.\nEl simulacro final consta de 60 preguntas tipo PMP® con tiempo cronometrado.\nNota mínima de aprobación: 60/100 (estándar PMI).",
                'reglas_pagos' => "El pago total del programa debe realizarse antes del inicio por su carácter intensivo.\nSe acepta fraccionamiento en máximo 2 cuotas con pago de la primera antes de la primera sesión.\nNo se realizan reembolsos una vez iniciado el programa.\nSe aceptan transferencia, depósito bancario o QR.",
                'reglas_conducta' => "El entorno intensivo requiere máxima concentración y disciplina durante todas las sesiones.\nApagar o silenciar dispositivos móviles durante las clases y simulacros.\nCompartir recursos y apuntes está permitido y fomentado entre compañeros.\nRespetar los estándares éticos del PMI es parte integral de la formación.",
                'reglas_plataformas' => "Las sesiones se realizan de forma presencial o por Zoom según el calendario del programa.\nEl enlace de Zoom y los materiales del PMBOK® se distribuyen por el grupo de WhatsApp.\nLos simulacros online se realizan en la plataforma Moodle de CENEFCO.\nNo está permitido compartir externamente los materiales del programa ni las preguntas de simulacro.",
                'reglas_derechos' => "Recibir formación alineada a la guía PMBOK® edición vigente por facilitadores con certificación PMP®.\nAcceder a materiales de estudio, banco de preguntas y guías de preparación durante el programa.\nRecibir orientación sobre los pasos para postular a la certificación CAPM® o PMP® del PMI.\nObtener su certificado de conclusión emitido por CENEFCO al aprobar el programa.",
            ],
        ];

        foreach ($registros as $registro) {
            $existe = DB::table('web_reglamento_programa')->where('id_programa', $registro['id_programa'])->exists();
            if (!$existe) {
                DB::table('web_reglamento_programa')->insertOrIgnore(array_merge($registro, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }
}
