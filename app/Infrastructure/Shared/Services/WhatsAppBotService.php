<?php

namespace App\Infrastructure\Shared\Services;

use App\Infrastructure\WhatsApp\BotHandlers\DocenteHandler;
use App\Infrastructure\WhatsApp\BotHandlers\InfoHandler;
use App\Infrastructure\WhatsApp\BotHandlers\InscripcionBotHandler;
use App\Infrastructure\WhatsApp\BotHandlers\MenuHandler;
use App\Infrastructure\WhatsApp\BotHandlers\ProgramaHandler;
use App\Infrastructure\WhatsApp\BotHandlers\SeguimientoHandler;
use App\Infrastructure\WhatsApp\ConversationManager;
use App\Infrastructure\WhatsApp\Enums\BotButton;
use App\Infrastructure\WhatsApp\Enums\BotState;

class WhatsAppBotService
{
    public function __construct(
        private readonly WhatsAppService $wa,
        private readonly ConversationManager $conv,
        private readonly MenuHandler $menu,
        private readonly ProgramaHandler $programa,
        private readonly InfoHandler $info,
        private readonly DocenteHandler $docente,
        private readonly SeguimientoHandler $seguimiento,
        private readonly InscripcionBotHandler $inscripcion,
        private readonly AgentService $agent,
    ) {}

    public function handleMessage(string $from, string $type, array $message, ?string $nombre = null): void
    {
        $conversacion = $this->conv->getOrCreate($from, $nombre);
        $estado = $conversacion->estado;

        $contenido = $message['text']['body']
            ?? $message['interactive']['button_reply']['title'] ?? null
            ?? $message['interactive']['list_reply']['title'] ?? null;
        $this->conv->logMensaje($conversacion, 'entrante', $type, $contenido, $message['id'] ?? null);

        if ($type === 'text') {
            $text = trim($message['text']['body'] ?? '');
            $this->routeByText($from, $text, $estado);

            return;
        }

        if ($type === 'interactive') {
            $id = $message['interactive']['button_reply']['id']
                ?? $message['interactive']['list_reply']['id']
                ?? '';
            $this->routeByButton($from, $id);

            return;
        }

        $this->handleUnsupportedType($from, $type);
    }

    private function handleUnsupportedType(string $from, string $type): void
    {
        $mensajes = [
            'image' => '🖼️ Recibí tu imagen, pero por ahora solo proceso mensajes de texto. ¿En qué puedo ayudarte?',
            'audio' => '🎙️ Recibí tu nota de voz, pero aún no proceso audios. Por favor escribe tu consulta.',
            'video' => '🎥 Recibí tu video, pero solo proceso mensajes de texto. ¿En qué puedo ayudarte?',
            'document' => '📄 Recibí tu documento, pero no proceso archivos. Escribe tu consulta en texto.',
            'sticker' => '😄 Bonito sticker! Si necesitas ayuda, escribe tu consulta o usa el *menú*.',
            'location' => '📍 Recibí tu ubicación. Si necesitas saber dónde estamos, escribe *ubicación*.',
            'contacts' => '👤 Recibí un contacto, pero solo proceso texto. ¿En qué puedo ayudarte?',
        ];

        $respuesta = $mensajes[$type] ?? 'Por el momento solo proceso mensajes de texto. ¿En qué puedo ayudarte?';
        $this->wa->sendText($from, $respuesta);
        $this->menu->handle($from);
    }

    private function routeByText(string $from, string $text, string $estado): void
    {
        if ($text === '') {
            $this->wa->sendText($from, '¿En qué puedo ayudarte? Escribe tu consulta o usa el menú. 😊');
            $this->menu->handle($from);

            return;
        }

        if ($estado === BotState::SOPORTE->value) {
            $this->wa->sendText($from, '📩 Tu mensaje fue recibido. Un asesor te atenderá pronto.');

            return;
        }

        if ($estado === BotState::CONSULTA_CI->value) {
            $this->seguimiento->buscarPorCI($from, $text);

            return;
        }

        $inscripcionStates = [
            BotState::INSCRIPCION_NOMBRE->value,
            BotState::INSCRIPCION_CI->value,
            BotState::INSCRIPCION_EMAIL->value,
            BotState::INSCRIPCION_CONFIRMAR->value,
        ];

        if (in_array($estado, $inscripcionStates, true)) {
            $contexto = (array) ($this->conv->getOrCreate($from)->contexto ?? []);

            if ($this->inscripcion->esCancelacion($text)) {
                $this->inscripcion->cancelar($from);

                return;
            }

            match ($estado) {
                BotState::INSCRIPCION_NOMBRE->value => $this->inscripcion->recibirNombre($from, $text, $contexto),
                BotState::INSCRIPCION_CI->value => $this->inscripcion->recibirCI($from, $text, $contexto),
                BotState::INSCRIPCION_EMAIL->value => $this->inscripcion->recibirEmail($from, $text, $contexto),
                BotState::INSCRIPCION_CONFIRMAR->value => $this->inscripcion->confirmar($from, $contexto),
                default => null,
            };

            return;
        }

        $intents = $this->detectarIntents($text);

        if (count($intents) === 1) {
            $this->dispatchKeyword($from, $intents[0]);

            return;
        }

        
        $this->wa->sendText($from, '⏳ Un momento, buscando la respuesta...');

        $historial = $this->conv->getUltimosMensajes($from, 10);
        $respuesta = $this->agent->responder($from, $text, $historial);

        if ($respuesta !== null) {
            $this->wa->sendText($from, $respuesta);

            return;
        }

        $this->menu->handle($from);
    }

    private function detectarIntents(string $text): array
    {
        $lower    = strtolower($this->removeAccents($text));
        $keywords = config('bot.keywords');
        $found    = [];

        foreach ($keywords as $intent => $words) {
            foreach ($words as $word) {
                if (str_contains($lower, $word)) {
                    $found[] = $intent;
                    break;
                }
            }
        }

        return array_unique($found);
    }

    private function dispatchKeyword(string $from, string $intent): void
    {
        match ($intent) {
            'saludo'       => $this->sendBienvenida($from),
            'programas'    => $this->programa->showLista($from),
            'eventos'      => $this->info->showEventos($from),
            'docentes'     => $this->docente->showLista($from),
            'faqs'         => $this->info->showFaqs($from),
            'inscripcion'  => $this->menu->handleInscripcion($from),
            'consulta'     => $this->seguimiento->pedirCI($from),
            'horario'      => $this->menu->handleHorario($from),
            'ubicacion'    => $this->menu->handleUbicacion($from),
            'pago'         => $this->handlePago($from),
            'acreditacion' => $this->handleAcreditacion($from),
            'soporte'      => $this->menu->handleSoporte($from),
            default        => $this->menu->handle($from),
        };
    }

    private function handlePago(string $from): void
    {
        $pagos = config('bot.cenefco.pagos');

        $this->wa->sendText($from,
            "💳 *Métodos de pago*\n\n"
            ."{$pagos['metodos']}\n\n"
            ."{$pagos['instrucciones']}\n\n"
            ."ℹ️ {$pagos['nota']}"
        );

        if (! empty($pagos['qr_image_url'])) {
            $this->wa->sendImage($from, $pagos['qr_image_url'], '📲 Escanea este QR para realizar tu pago.');
        }
    }

    private function handleAcreditacion(string $from): void
    {
        $acred = config('bot.cenefco.acreditacion');
        $this->wa->sendText($from,
            "🎓 *Acreditación e Institucionalidad*\n\n"
            ."✅ Aval universitario: {$acred['aval']}\n"
            ."📋 {$acred['resolucion']}\n"
            ."🏛️ Autorizados por: {$acred['autoriza']}\n\n"
            ."{$acred['descripcion']}"
        );
    }

    private function routeByButton(string $from, string $id): void
    {
        match (true) {
            $id === BotButton::MENU->value => $this->menu->handle($from),
            $id === BotButton::PROGRAMAS->value => $this->programa->showLista($from),
            $id === BotButton::EVENTOS->value => $this->info->showEventos($from),
            $id === BotButton::DOCENTES->value => $this->docente->showLista($from),
            $id === BotButton::FAQS->value => $this->info->showFaqs($from),
            $id === BotButton::INSCRIPCION->value => $this->menu->handleInscripcion($from),
            $id === BotButton::HORARIO->value => $this->menu->handleHorario($from),
            $id === BotButton::UBICACION->value => $this->menu->handleUbicacion($from),
            $id === BotButton::SOPORTE->value => $this->menu->handleSoporte($from),

            str_starts_with($id, BotButton::PREFIX_PROGRAMA) => $this->programa->showDetalle($from, (int) substr($id, strlen(BotButton::PREFIX_PROGRAMA))),
            str_starts_with($id, BotButton::PREFIX_EVENTO) => $this->info->showDetalleEvento($from, (int) substr($id, strlen(BotButton::PREFIX_EVENTO))),
            str_starts_with($id, BotButton::PREFIX_DOCENTE) => $this->docente->showDetalle($from, (int) substr($id, strlen(BotButton::PREFIX_DOCENTE))),
            str_starts_with($id, BotButton::PREFIX_INSCRIBIR) => $this->inscripcion->iniciar($from, (int) substr($id, strlen(BotButton::PREFIX_INSCRIBIR))),

            $id === BotButton::INSCRIPCION_CONFIRMAR->value => $this->inscripcion->confirmar($from, (array) ($this->conv->getOrCreate($from)->contexto ?? [])),
            $id === BotButton::INSCRIPCION_CANCELAR->value => $this->inscripcion->cancelar($from),

            default => $this->menu->handle($from),
        };
    }

    public function sendBienvenida(string $from): void
    {
        $cenefco = config('bot.cenefco.nombre');
        $conv = $this->conv->getOrCreate($from);
        $saludo = $conv->nombre ? "¡Hola, *{$conv->nombre}*!" : '¡Hola!';

        $this->wa->sendText($from, "👋 {$saludo} Bienvenido/a al chatbot de *{$cenefco}*.\n\nEstoy aquí para ayudarte con información sobre nuestros programas, docentes, eventos e inscripciones.");
        $this->menu->handle($from);
    }

    public function sendPlantillaConfirmacion(string $to, string $numPedido, string $total): void
    {
        $this->wa->sendTemplate($to, 'confirmacion_pedido', 'es', [
            ['type' => 'text', 'text' => $numPedido],
            ['type' => 'text', 'text' => $total],
        ]);
    }

    public function sendPlantillaEntrega(string $to, string $numPedido): void
    {
        $this->wa->sendTemplate($to, 'estado_entrega', 'es', [
            ['type' => 'text', 'text' => $numPedido],
        ]);
    }

    public function sendPlantillaPromocion(string $to, string $descuento, string $fechaFin): void
    {
        $this->wa->sendTemplate($to, 'promocion_oferta', 'es', [
            ['type' => 'text', 'text' => $descuento],
            ['type' => 'text', 'text' => $fechaFin],
        ]);
    }

    private function removeAccents(string $text): string
    {
        $result = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

        return $result !== false ? $result : $text;
    }
}
