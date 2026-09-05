<?php

namespace App\Infrastructure\WhatsApp\BotHandlers;

use App\Infrastructure\Shared\Services\WhatsAppService;
use App\Infrastructure\WhatsApp\ConversationManager;
use App\Infrastructure\WhatsApp\Enums\BotButton;
use App\Infrastructure\WhatsApp\Enums\BotState;
use Illuminate\Support\Facades\DB;

class MenuHandler
{
    public function __construct(
        private WhatsAppService $wa,
        private ConversationManager $conv
    ) {}

    public function handle(string $from): void
    {
        $nombre = config('bot.cenefco.nombre');

        $this->wa->sendList(
            $from,
            "🎓 {$nombre}",
            '¿En qué puedo ayudarte hoy?',
            'Formación continua de excelencia',
            'Ver opciones',
            [
                [
                    'title' => '📚 Oferta Académica',
                    'rows' => [
                        ['id' => BotButton::PROGRAMAS->value, 'title' => '📚 Programas y Cursos',    'description' => 'Diplomados, cursos y especializaciones'],
                        ['id' => BotButton::DOCENTES->value,  'title' => '👨‍🏫 Nuestros Docentes',    'description' => 'Conoce a nuestros expertos'],
                        ['id' => BotButton::EVENTOS->value,   'title' => '📅 Eventos Académicos',    'description' => 'Conferencias, talleres y más'],
                    ],
                ],
                [
                    'title' => '📋 Información',
                    'rows' => [
                        ['id' => BotButton::INSCRIPCION->value, 'title' => '📝 Inscripciones',       'description' => 'Cómo inscribirse en nuestros programas'],
                        ['id' => BotButton::FAQS->value,        'title' => '❓ Preguntas Frecuentes', 'description' => 'Respuestas a tus dudas comunes'],
                        ['id' => BotButton::HORARIO->value,     'title' => '🕐 Horarios de Atención', 'description' => 'Cuándo puedes visitarnos'],
                        ['id' => BotButton::UBICACION->value,   'title' => '📍 Ubicación',            'description' => 'Dónde encontrarnos'],
                        ['id' => BotButton::SOPORTE->value,     'title' => '📞 Hablar con Asesor',    'description' => 'Atención personalizada'],
                    ],
                ],
            ]
        );

        $this->conv->setState($from, BotState::MENU->value);

        $this->wa->sendButtons($from, '¿Necesitas hablar con un asesor?', [
            ['id' => BotButton::SOPORTE->value, 'title' => '🙋 Hablar con asesor'],
        ]);
    }

    public function handleHorario(string $from): void
    {
        $nombre = config('bot.cenefco.nombre');
        $horarios = config('bot.cenefco.horarios');

        $texto = "🕐 *Horarios de atención — {$nombre}*\n\n";
        foreach ($horarios as $dia => $hora) {
            $texto .= "📅 *{$dia}:* {$hora}\n";
        }

        $this->wa->sendText($from, $texto);
        $this->handle($from);
    }

    public function handleUbicacion(string $from): void
    {
        $loc = config('bot.cenefco.ubicacion');
        $nombre = config('bot.cenefco.nombre');

        $this->wa->sendLocation(
            $from,
            $loc['latitude'],
            $loc['longitude'],
            $nombre,
            $loc['direccion']
        );

        $this->wa->sendText(
            $from,
            "📍 *{$loc['direccion']}*\n🏷️ Referencia: {$loc['referencia']}\n\n🗺️ {$loc['maps_link']}"
        );

        $this->handle($from);
    }

    public function handleSoporte(string $from): void
    {
        $email = config('bot.cenefco.contacto.email');
        $cenefco = config('bot.cenefco.nombre');

        $convRow = DB::table('whatsapp_conversaciones')->where('phone', $from)->first(['nombre', 'contexto']);
        $contexto = $convRow ? (json_decode($convRow->contexto ?? '{}', true) ?? []) : [];

        $nombreUsuario = $convRow->nombre ?? null;
        $pid = $contexto['ins_programa_nombre'] ?? null;
        $programa = $pid ?? (($contexto['programa_id'] ?? null)
            ? DB::table('t_programa')->where('id_programa', $contexto['programa_id'])->value('nombre_programa')
            : null);

        $asesor = DB::table('web_asesor')
            ->where('activo', true)
            ->where('disponible', true)
            ->inRandomOrder()
            ->first(['id', 'nombre', 'telefono']);

        if ($asesor) {
            DB::table('whatsapp_conversaciones')
                ->where('phone', $from)
                ->update(['asesor_id' => $asesor->id, 'updated_at' => now()->toDateTimeString()]);

            $msgParts = [];
            $msgParts[] = 'Hola, soy '.($nombreUsuario ?? 'un estudiante');
            $msgParts[] = "y fui derivado desde el chatbot de {$cenefco}.";
            if ($programa) {
                $msgParts[] = "Estoy interesado/a en el programa: {$programa}.";
            }
            $msgParts[] = '¿Podrías orientarme?';

            $msgWa = implode(' ', $msgParts);
            $tel = preg_replace('/[^0-9]/', '', $asesor->telefono);
            $waUrl = 'https://wa.me/'.$tel.'?text='.rawurlencode($msgWa);

            $this->wa->sendText(
                $from,
                "📞 *Atención Personalizada*\n\n".
                "Te hemos asignado con el asesor *{$asesor->nombre}*.\n\n".
                "Puedes contactarlo directamente:\n".
                "📲 {$asesor->telefono}\n".
                "✉️ {$email}\n\n".
                '_En breve se pondrá en contacto contigo._'
            );

            $this->wa->sendText(
                $from,
                "💬 *¿Prefieres escribirle ahora?*\n{$waUrl}",
                true
            );
        } else {
            $tel = config('bot.cenefco.contacto.telefono');

            $this->wa->sendText(
                $from,
                "📞 *Atención Personalizada*\n\nUn asesor académico te atenderá en breve.\n\nTambién puedes contactarnos directamente:\n📲 {$tel}\n✉️ {$email}"
            );
        }

        $this->conv->setState($from, BotState::SOPORTE->value);
    }

    public function handleInscripcion(string $from): void
    {
        $contacto = config('bot.cenefco.contacto');
        $web = $contacto['web'];

        $texto = "📝 *Proceso de Inscripción*\n\n";
        $texto .= "Para inscribirte en cualquiera de nuestros programas sigue estos pasos:\n\n";
        $texto .= "1️⃣ Revisa nuestra oferta académica y elige tu programa\n";
        $texto .= "2️⃣ Completa el formulario de pre-inscripción en línea\n";
        $texto .= "3️⃣ Espera la confirmación de disponibilidad\n";
        $texto .= "4️⃣ Realiza el pago según las opciones disponibles\n\n";
        $texto .= "📌 *Requisitos generales:*\n";
        $texto .= "• Título a nivel licenciatura (para diplomados)\n";
        $texto .= "• Fotocopia de CI\n";
        $texto .= "• Foto carnet reciente\n\n";
        $texto .= "🌐 Pre-inscripción en línea: {$web}";

        $this->wa->sendText($from, $texto);

        $this->wa->sendButtons($from, '¿Necesitas más información?', [
            ['id' => BotButton::PROGRAMAS->value, 'title' => '📚 Ver programas'],
            ['id' => BotButton::SOPORTE->value,   'title' => '📞 Hablar con asesor'],
            ['id' => BotButton::MENU->value,       'title' => '🏠 Menú principal'],
        ]);
    }
}
