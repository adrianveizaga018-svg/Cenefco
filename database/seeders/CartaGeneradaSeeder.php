<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartaGeneradaSeeder extends Seeder
{
    public function run(): void
    {
        $generadas = [
            [
                'id_cartagen'  => 1,
                'num_carta'    => 1,
                'id_us'        => 1,
                'id_cartamod'  => 2,
                'textocarta'   => 'Se certifica que el/la estudiante Carlos Alberto Mamani Quispe, con C.I. 1234567 exp. LP, se encuentra debidamente inscrito/a en el programa de Diplomado en Gestión Pública y Administración del Estado, Gestión 2025-I, habiendo cumplido satisfactoriamente con los requisitos académicos establecidos.',
                'textocarta1'  => 'A solicitud de parte interesada y para los fines que el portador estime convenientes, se extiende la presente CERTIFICACIÓN DE ESTUDIOS.',
                'textocarta3'  => 'Se extiende la presente certificación a solicitud del interesado/a para los fines que estime convenientes.',
                'usar_encabezado_pie_estandar' => 1,
                'cp_nro_contrato'    => 1001,
                'cp_gestion_contrato'=> '2025-I',
            ],
            [
                'id_cartagen'  => 2,
                'num_carta'    => 2,
                'id_us'        => 2,
                'id_cartamod'  => 2,
                'textocarta'   => 'Se certifica que la estudiante Ana María Flores Condori, con C.I. 2345678 exp. CBBA, se encuentra debidamente inscrita en el programa de Maestría en Administración de Empresas (MBA), Gestión 2025-II, habiendo cumplido satisfactoriamente con los requisitos académicos establecidos.',
                'textocarta1'  => 'A solicitud de parte interesada y para los fines que el portador estime convenientes, se extiende la presente CERTIFICACIÓN DE ESTUDIOS.',
                'textocarta3'  => 'Se extiende la presente certificación a solicitud del interesado/a para los fines que estime convenientes.',
                'usar_encabezado_pie_estandar' => 1,
                'cp_nro_contrato'    => 1002,
                'cp_gestion_contrato'=> '2025-II',
            ],
            [
                'id_cartagen'  => 3,
                'num_carta'    => 3,
                'id_us'        => 3,
                'id_cartamod'  => 1,
                'textocarta'   => 'Por medio de la presente, nos complace presentar a CENEFCO como institución educativa comprometida con la formación académica de calidad. El Ing. Luis Eduardo Gutierrez Vargas participa activamente en nuestros programas de formación continua.',
                'textocarta1'  => 'Nuestra institución cuenta con programas avalados por la Universidad San Francisco de Asís (USFA).',
                'textocarta3'  => 'Quedamos a su disposición para brindar mayor información.',
                'usar_encabezado_pie_estandar' => 1,
                'cp_nro_contrato'    => 1003,
                'cp_gestion_contrato'=> '2026-I',
            ],
            [
                'id_cartagen'  => 4,
                'num_carta'    => 4,
                'id_us'        => 4,
                'id_cartamod'  => 5,
                'textocarta'   => 'Por medio de la presente, nos permitimos informarle sobre el estado de su cuenta académica correspondiente al período 2026-I. Se le notifica que existe un saldo pendiente de pago de Bs. 1,200 correspondiente a segunda cuota de matrícula del programa de Especialización en Marketing Digital.',
                'textocarta1'  => 'Le solicitamos regularizar dicho monto a la brevedad posible para evitar inconvenientes en su proceso académico.',
                'textocarta3'  => 'Para realizar su pago, sírvase acercarse a nuestra oficina de administración o comunicarse al 70000000.',
                'usar_encabezado_pie_estandar' => 1,
                'cp_nro_contrato'    => 1004,
                'cp_gestion_contrato'=> '2026-I',
            ],
            [
                'id_cartagen'  => 5,
                'num_carta'    => 5,
                'id_us'        => 5,
                'id_cartamod'  => 4,
                'textocarta'   => 'Nos complace extenderle una cordial invitación al Seminario de Actualización Tributaria 2026 organizado por CENEFCO.',
                'textocarta1'  => 'El evento se realizará el día 15 de junio de 2026 a horas 09:00, en el Auditorio Central de USFA, La Paz. Contaremos con la participación de expertos en tributación y el Servicio de Impuestos Nacionales (SIN).',
                'textocarta3'  => 'Para confirmar su asistencia, comunicarse al 70000000 o al correo info@cenefco.edu.bo.',
                'usar_encabezado_pie_estandar' => 1,
                'cp_nro_contrato'    => null,
                'cp_gestion_contrato'=> '2026',
            ],
        ];

        $now = now()->toDateTimeString();
        foreach ($generadas as $g) {
            $existe = DB::table('t_cartagen')->where('id_cartagen', $g['id_cartagen'])->exists();
            if ($existe) continue;
            DB::table('t_cartagen')->insertOrIgnore(array_merge($g, [
                'id_us_reg' => 1,
                'estado'    => 1,
                'fecha_reg' => $now,
            ]));
        }

        $this->command->info('✓ ' . count($generadas) . ' cartas generadas insertadas en t_cartagen.');
    }
}
