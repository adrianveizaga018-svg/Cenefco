<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartaModeloSeeder extends Seeder
{
    public function run(): void
    {
        $modelos = [
            [
                'id_cartamod'  => 1,
                'num_cartamod' => 1,
                'nombremodelo' => 'Carta de Presentación Institucional',
                'textocarta'   => 'Por medio de la presente, nos complace presentar a la Institución Educativa CENEFCO, como una organización comprometida con la formación académica y profesional de calidad en el Estado Plurinacional de Bolivia.',
                'textocarta1'  => 'Nuestra institución cuenta con programas de diplomados, maestrías y especializaciones avalados por la Universidad San Francisco de Asís (USFA), garantizando la excelencia educativa y el desarrollo integral de nuestros estudiantes.',
                'textocarta3'  => 'Quedamos a su entera disposición para brindarle mayor información sobre nuestra oferta académica y los requisitos de admisión.',
                'texto_carta'  => null,
                'usar_encabezado_pie_estandar' => 1,
            ],
            [
                'id_cartamod'  => 2,
                'num_cartamod' => 2,
                'nombremodelo' => 'Carta de Certificación de Estudios',
                'textocarta'   => 'A solicitud de parte interesada y para los fines que el portador estime convenientes, se extiende la presente CERTIFICACIÓN DE ESTUDIOS.',
                'textocarta1'  => 'Se certifica que el/la estudiante {NOMBRE_ESTUDIANTE}, con C.I. {CI_ESTUDIANTE} exp. {EXPEDIDO}, se encuentra debidamente inscrito/a en el programa de {NOMBRE_PROGRAMA}, Gestión {GESTION}, habiendo cumplido satisfactoriamente con los requisitos académicos establecidos.',
                'textocarta3'  => 'Se extiende la presente certificación a solicitud del interesado/a para los fines que estime convenientes.',
                'texto_carta'  => null,
                'usar_encabezado_pie_estandar' => 1,
            ],
            [
                'id_cartamod'  => 3,
                'num_cartamod' => 3,
                'nombremodelo' => 'Carta de Recomendación Académica',
                'textocarta'   => 'Mediante la presente nota, tengo el agrado de dirigirme a usted para extender mi más sincera recomendación académica y profesional.',
                'textocarta1'  => 'El/La estudiante {NOMBRE_ESTUDIANTE} ha demostrado durante su formación en nuestra institución un desempeño académico sobresaliente, evidenciando capacidades analíticas, trabajo en equipo y compromiso con la excelencia profesional.',
                'textocarta3'  => 'Por lo expuesto, recomiendo ampliamente al/la mencionado/a profesional para cualquier posición académica o laboral que se le presente, ya que estoy seguro/a que desempeñará sus funciones con eficiencia y dedicación.',
                'texto_carta'  => null,
                'usar_encabezado_pie_estandar' => 1,
            ],
            [
                'id_cartamod'  => 4,
                'num_cartamod' => 4,
                'nombremodelo' => 'Carta de Convocatoria a Evento Académico',
                'textocarta'   => 'Nos complace extenderle una cordial invitación al evento académico organizado por CENEFCO, en el marco de nuestro compromiso con la formación continua y la actualización profesional.',
                'textocarta1'  => 'El evento se realizará el día {FECHA_EVENTO} a horas {HORA_EVENTO}, en las instalaciones de {LUGAR_EVENTO}. Contaremos con la participación de destacados profesionales y académicos del área.',
                'textocarta3'  => 'Esperamos contar con su distinguida presencia. Para confirmar su asistencia, comunicarse al {TELEFONO_CONTACTO} o al correo {EMAIL_CONTACTO}.',
                'texto_carta'  => null,
                'usar_encabezado_pie_estandar' => 1,
            ],
            [
                'id_cartamod'  => 5,
                'num_cartamod' => 5,
                'nombremodelo' => 'Carta de Notificación de Pago',
                'textocarta'   => 'Por medio de la presente, nos permitimos informarle sobre el estado de su cuenta académica correspondiente al período {PERIODO_ACADEMICO}.',
                'textocarta1'  => 'Se le notifica que existe un saldo pendiente de pago de Bs. {MONTO_PENDIENTE} correspondiente a {CONCEPTO_PAGO}. Le solicitamos regularizar dicho monto a la brevedad posible para evitar inconvenientes en su proceso académico.',
                'textocarta3'  => 'Para realizar su pago o aclarar cualquier consulta, sírvase acercarse a nuestra oficina de administración o comunicarse al {TELEFONO_ADMIN}.',
                'texto_carta'  => null,
                'usar_encabezado_pie_estandar' => 1,
            ],
        ];

        $now = now()->toDateTimeString();
        foreach ($modelos as $m) {
            $existe = DB::table('t_cartamodelo')->where('id_cartamod', $m['id_cartamod'])->exists();
            if ($existe) continue;
            DB::table('t_cartamodelo')->insertOrIgnore(array_merge($m, [
                'id_us_reg' => 1,
                'estado'    => 1,
                'fecha_reg' => $now,
            ]));
        }

        $this->command->info('✓ ' . count($modelos) . ' modelos de cartas insertados en t_cartamodelo.');
    }
}
