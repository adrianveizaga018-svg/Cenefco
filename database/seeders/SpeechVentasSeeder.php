<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpeechVentasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('web_speech_ventas')->truncate();

        $now = now();

        $speeches = [
            
            [
                'titulo'         => 'Saludo inicial y presentación CENEFCO',
                'categoria'      => 'bienvenida',
                'contenido'      => "¡Hola! 👋 Bienvenido/a a *CENEFCO — Centro de Formación Continua*.\n\nSomos una institución especializada en diplomados, cursos y programas de formación profesional de alta calidad.\n\nEstoy aquí para ayudarte a encontrar el programa ideal para ti. ¿En qué puedo orientarte hoy?",
                'palabras_clave' => 'hola, buenos días, buenas tardes, saludo, información, ayuda',
                'activo'         => true,
                'orden'          => 1,
            ],

            
            [
                'titulo'         => 'Presentación de oferta académica',
                'categoria'      => 'presentacion',
                'contenido'      => "En CENEFCO ofrecemos:\n\n📚 *Diplomados* — Programas intensivos de 3 a 6 meses con certificación avalada.\n🎓 *Cursos de especialización* — Formación puntual en habilidades específicas.\n💼 *Programas ejecutivos* — Diseñados para profesionales en actividad.\n\nTodos nuestros programas cuentan con *docentes expertos*, modalidad presencial y virtual, y certificación reconocida a nivel nacional.\n\n¿Qué área te interesa explorar?",
                'palabras_clave' => 'cursos, diplomados, programas, oferta, catálogo, qué ofrecen, qué tienen',
                'activo'         => true,
                'orden'          => 2,
            ],

            
            [
                'titulo'         => 'Consulta de precios y costos',
                'categoria'      => 'beneficios',
                'contenido'      => "Nuestros programas tienen precios accesibles y contamos con *opciones de pago en cuotas* para que puedas iniciar sin preocupaciones.\n\nEl costo varía según el programa y la modalidad. Para darte el precio exacto del curso que te interesa, ¿podrías decirme cuál programa estás evaluando?\n\nTambién contamos con *descuentos especiales* para inscripciones anticipadas y grupos. 🎯",
                'palabras_clave' => 'precio, costo, cuánto, cuánto vale, cuánto cuesta, pago, inversión, tarifa',
                'activo'         => true,
                'orden'          => 3,
            ],

            
            [
                'titulo'         => 'Objeción: "Es muy caro"',
                'categoria'      => 'objeciones',
                'contenido'      => "Entiendo que el presupuesto es importante. 💡\n\nTe cuento que en CENEFCO puedes pagar en *cuotas sin recargo*, lo que hace que la inversión sea muy manejable mes a mes.\n\nAdemás, piensa en lo que este certificado puede hacer por tu carrera: *mayor empleabilidad, ascensos y mejores ingresos*. La formación profesional es una de las inversiones con mayor retorno.\n\n¿Te gustaría que te armemos un plan de pago personalizado?",
                'palabras_clave' => 'caro, costoso, precio alto, no tengo dinero, fuera de presupuesto, muy costoso',
                'activo'         => true,
                'orden'          => 4,
            ],

            
            [
                'titulo'         => 'Objeción: "No tengo tiempo"',
                'categoria'      => 'objeciones',
                'contenido'      => "¡Completamente entendible! Por eso diseñamos nuestros programas pensando en personas ocupadas. ⏰\n\nContamos con:\n• Clases en *horarios flexibles* (noches y fines de semana)\n• Modalidad *virtual* para que estudies desde donde estés\n• Clases grabadas disponibles cuando tú puedas\n\nMuchos de nuestros estudiantes trabajan a tiempo completo y logran completar el programa sin inconvenientes.\n\n¿Qué horario se ajustaría mejor a tu rutina?",
                'palabras_clave' => 'tiempo, ocupado, trabajo, horario, no puedo, sin tiempo, muy ocupado',
                'activo'         => true,
                'orden'          => 5,
            ],

            
            [
                'titulo'         => 'Objeción: "Lo voy a pensar"',
                'categoria'      => 'objeciones',
                'contenido'      => "Claro, es una decisión importante y es válido tomarse un momento. 🤔\n\nPero te comparto algo: nuestros cupos son *limitados* y las inscripciones cierran próximamente. Además, los precios especiales vigentes tienen fecha de vencimiento.\n\n¿Hay alguna duda puntual que te esté frenando? Con gusto la resolvemos ahora mismo para que puedas decidir con toda la información. 😊",
                'palabras_clave' => 'pensar, consultarlo, después, luego, más adelante, voy a ver, me lo pienso',
                'activo'         => true,
                'orden'          => 6,
            ],

            
            [
                'titulo'         => 'Cierre con urgencia — cupos limitados',
                'categoria'      => 'cierre',
                'contenido'      => "🔔 ¡Atención! Este programa tiene *cupos muy limitados* y quedan pocas vacantes disponibles.\n\nPara asegurar tu lugar solo necesitas:\n✅ Completar el formulario de inscripción\n✅ Realizar el primer pago\n\nTe tomará menos de 10 minutos y tu cupo quedará reservado de inmediato.\n\n¿Empezamos con tu inscripción ahora?",
                'palabras_clave' => 'cupos, inscribir, reservar, últimas plazas, fecha límite, inscripción',
                'activo'         => true,
                'orden'          => 7,
            ],

            
            [
                'titulo'         => 'Cierre con descuento por inscripción temprana',
                'categoria'      => 'cierre',
                'contenido'      => "🎁 Tenemos una oferta especial que vence pronto.\n\nSi realizas tu inscripción *antes del cierre de la promoción*, accedes a un *descuento exclusivo* que no estará disponible después.\n\nEs la oportunidad perfecta para invertir en tu formación y ahorrar al mismo tiempo.\n\n¿Quieres aprovechar esta oferta hoy?",
                'palabras_clave' => 'descuento, oferta, promoción, precio especial, ahorro, rebaja',
                'activo'         => true,
                'orden'          => 8,
            ],

            
            [
                'titulo'         => 'Seguimiento a prospecto interesado',
                'categoria'      => 'seguimiento',
                'contenido'      => "Hola de nuevo 👋 Hace unos días conversamos sobre el programa que te interesaba en CENEFCO.\n\nQuería saber si ya pudiste evaluar la información que te compartimos y si tienes alguna consulta adicional.\n\nEstamos aquí para ayudarte y queremos asegurarnos de que tengas todo lo que necesitas para tomar la mejor decisión. 😊",
                'palabras_clave' => 'seguimiento, recordatorio, retomar, conversación anterior',
                'activo'         => true,
                'orden'          => 9,
            ],

            
            [
                'titulo'         => 'Reactivación de prospecto inactivo',
                'categoria'      => 'reactivacion',
                'contenido'      => "¡Hola! 👋 Te escribimos desde CENEFCO.\n\nSabemos que la vida puede estar muy agitada, pero queríamos recordarte que el programa que consultaste sigue disponible.\n\n¿Sigue siendo de tu interés o te puedo orientar sobre otras opciones que se adapten mejor a lo que necesitas ahora?\n\nEstamos para ayudarte cuando estés listo/a. 🙌",
                'palabras_clave' => 'reactivar, sin respuesta, prospecto frío, retomar contacto',
                'activo'         => true,
                'orden'          => 10,
            ],

            
            [
                'titulo'         => 'Beneficios del certificado CENEFCO',
                'categoria'      => 'beneficios',
                'contenido'      => "Nuestros certificados tienen *respaldo académico real* y son reconocidos en el ámbito profesional boliviano. 🎓\n\nAl completar tu programa obtienes:\n• *Certificado de conclusión* con sello institucional\n• *Registro en base de datos* verificable por QR\n• Constancia de horas académicas\n• Acceso a nuestra *red de egresados*\n\nInversión en conocimiento = crecimiento profesional garantizado.",
                'palabras_clave' => 'certificado, certificación, reconocido, válido, oficial, título, diploma',
                'activo'         => true,
                'orden'          => 11,
            ],

            
            [
                'titulo'         => 'Información sobre modalidad virtual',
                'categoria'      => 'presentacion',
                'contenido'      => "Nuestra modalidad *virtual* es ideal para quienes no pueden asistir de forma presencial. 💻\n\nCon ella tienes acceso a:\n• Clases en vivo por videollamada con interacción real\n• *Grabaciones disponibles* para revisar cuando quieras\n• Material descargable y recursos digitales\n• Tutoría personalizada por WhatsApp\n• Evaluaciones y certificación al completar\n\n¡Estudias desde la comodidad de tu hogar sin perder calidad educativa!",
                'palabras_clave' => 'virtual, online, en línea, desde casa, remoto, internet, Zoom',
                'activo'         => true,
                'orden'          => 12,
            ],
        ];

        foreach ($speeches as $speech) {
            DB::table('web_speech_ventas')->insert(array_merge($speech, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        $this->command->info('✅ SpeechVentasSeeder: ' . count($speeches) . ' speeches insertados.');
    }
}
