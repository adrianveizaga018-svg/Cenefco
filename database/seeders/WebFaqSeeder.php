<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebFaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ['pregunta' => '¿Cómo me inscribo en un programa?',
                'respuesta' => 'Puedes inscribirte completando el formulario de inscripción disponible en la página de cada programa o contactándonos directamente por WhatsApp.',
                'categoria' => 'inscripciones', 'orden' => 1],

            ['pregunta' => '¿Qué documentos necesito para inscribirme?',
                'respuesta' => 'Debes presentar fotocopia de tu carnet de identidad (anverso y reverso) y, en caso de tener grado académico, una copia de tu título o certificado correspondiente.',
                'categoria' => 'inscripciones', 'orden' => 2],

            ['pregunta' => '¿Los programas tienen certificación?',
                'respuesta' => 'Sí. Al concluir y aprobar el programa recibirás un certificado/diploma con respaldo institucional, el cual puede ser verificado en nuestra plataforma.',
                'categoria' => 'certificados', 'orden' => 3],

            ['pregunta' => '¿Cómo verifico la autenticidad de mi certificado?',
                'respuesta' => 'Puedes verificar la autenticidad de cualquier certificado ingresando el código de verificación en nuestra sección de "Verificar Certificado".',
                'categoria' => 'certificados', 'orden' => 4],

            ['pregunta' => '¿Cuáles son los medios de pago disponibles?',
                'respuesta' => 'Aceptamos depósito bancario, transferencia interbancaria y pago por QR. Al finalizar la inscripción recibirás los datos bancarios en tu correo electrónico.',
                'categoria' => 'pagos', 'orden' => 5],

            ['pregunta' => '¿Los programas son presenciales u online?',
                'respuesta' => 'Ofrecemos programas en modalidad presencial, virtual y semipresencial. La modalidad de cada programa está indicada en su descripción.',
                'categoria' => 'general', 'orden' => 6],

            ['pregunta' => '¿Puedo cancelar mi inscripción y solicitar un reembolso?',
                'respuesta' => 'Las políticas de cancelación y reembolso varían según el programa. Por favor contáctanos directamente para más información sobre tu caso específico.',
                'categoria' => 'pagos', 'orden' => 7],

            ['pregunta' => '¿Cómo accedo a la plataforma virtual?',
                'respuesta' => 'Una vez confirmada tu inscripción y pago recibirás tus credenciales de acceso al campus virtual en el correo electrónico registrado.',
                'categoria' => 'plataforma', 'orden' => 8],
        ];

        foreach ($faqs as $faq) {
            DB::table('web_faq')->updateOrInsert(
                ['pregunta' => $faq['pregunta']],
                array_merge($faq, ['activo' => true, 'programa_id' => null])
            );
        }
    }
}
