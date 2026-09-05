<?php

namespace App\Infrastructure\WhatsApp\BotHandlers;

use App\Infrastructure\Shared\Services\WhatsAppService;
use App\Infrastructure\WhatsApp\ConversationManager;
use App\Infrastructure\WhatsApp\Enums\BotButton;
use App\Infrastructure\WhatsApp\Enums\BotState;
use Illuminate\Support\Facades\DB;

class InfoHandler
{
    public function __construct(
        private WhatsAppService $wa,
        private ConversationManager $conv,
        private MenuHandler $menu
    ) {}

    public function showEventos(string $from): void
    {
        $eventos = DB::table('web_evento')
            ->whereNull('deleted_at')
            ->where('estado', 'publicado')
            ->orderByDesc('fecha_inicio')
            ->limit(5)
            ->get(['id', 'titulo', 'entradilla', 'fecha_inicio', 'fecha_fin', 'lugar', 'modalidad', 'gratuito', 'precio', 'url_registro', 'imagen_url']);

        if ($eventos->isEmpty()) {
            $this->wa->sendText($from, '😔 No hay eventos registrados en este momento.');
            $this->menu->handle($from);

            return;
        }

        $this->wa->sendText($from, '📅 *Próximos Eventos Académicos*');

        foreach ($eventos as $evento) {
            $titulo = mb_substr($evento->titulo ?? '', 0, 100);
            $body = "*{$titulo}*";

            if ($evento->fecha_inicio) {
                $body .= "\n🗓️ ".date('d/m/Y H:i', strtotime($evento->fecha_inicio));
            }
            if ($evento->lugar) {
                $body .= "\n📍 ".mb_substr($evento->lugar, 0, 80);
            }
            if ($evento->modalidad) {
                $body .= "\n🖥️ ".ucfirst($evento->modalidad);
            }
            if ($evento->entradilla) {
                $body .= "\n\n".mb_substr($evento->entradilla, 0, 150);
            }

            $imageUrl = null;
            if ($evento->imagen_url) {
                $raw = $evento->imagen_url;
                $imageUrl = str_starts_with($raw, 'http')
                    ? $raw
                    : rtrim(config('app.url'), '/').'/'.ltrim($raw, '/');
            }

            $buttons = [
                ['id' => BotButton::PREFIX_EVENTO.$evento->id, 'title' => 'Ver detalle'],
            ];

            if ($imageUrl) {
                try {
                    $this->wa->sendButtonsWithImage($from, $imageUrl, $body, $buttons, 'Evento Académico');

                    continue;
                } catch (\Throwable) {
                }
            }

            $this->wa->sendButtons($from, $body, $buttons, '', 'Evento Académico');
        }

        $portalUrl = rtrim(config('bot.cenefco.contacto.web', config('app.url')), '/').'/eventos';
        $this->wa->sendCtaUrl($from, '¿Quieres ver todos los eventos?', '📅 Ver todos los eventos', $portalUrl);

        $this->wa->sendButtons($from, '¿Qué deseas hacer?', [
            ['id' => BotButton::MENU->value, 'title' => '🏠 Menú principal'],
        ]);

        $this->conv->setState($from, BotState::EVENTOS->value);
    }

    public function showDetalleEvento(string $from, int $eventoId): void
    {
        $evento = DB::table('web_evento')
            ->where('id', $eventoId)
            ->whereNull('deleted_at')
            ->first();

        if (! $evento) {
            $this->wa->sendText($from, '⚠️ Evento no encontrado.');
            $this->showEventos($from);

            return;
        }

        $texto = "📅 *{$evento->titulo}*\n\n";

        if ($evento->fecha_inicio) {
            $texto .= '🗓️ *Inicio:* '.date('d/m/Y H:i', strtotime($evento->fecha_inicio))."\n";
        }
        if ($evento->fecha_fin) {
            $texto .= '🗓️ *Fin:* '.date('d/m/Y H:i', strtotime($evento->fecha_fin))."\n";
        }
        if ($evento->lugar) {
            $texto .= "📍 *Lugar:* {$evento->lugar}\n";
        }
        if ($evento->modalidad) {
            $texto .= '🖥️ *Modalidad:* '.ucfirst($evento->modalidad)."\n";
        }

        $costo = $evento->gratuito ? 'Gratuito' : ('Bs. '.number_format((float) $evento->precio, 2));
        $texto .= "💰 *Costo:* {$costo}\n";

        if ($evento->descripcion) {
            $texto .= "\n".mb_substr($evento->descripcion, 0, 500);
        }

        if ($evento->url_transmision) {
            $texto .= "\n\n🔴 *Transmisión:* {$evento->url_transmision}";
        }

        $this->wa->sendText($from, $texto);

        if ($evento->url_registro) {
            $this->wa->sendCtaUrl($from, '¿Quieres registrarte en este evento?', '📝 Registrarme', $evento->url_registro);
        }

        $this->wa->sendButtons($from, '¿Qué deseas hacer?', [
            ['id' => BotButton::EVENTOS->value, 'title' => '📅 Ver más eventos'],
            ['id' => BotButton::MENU->value,     'title' => '🏠 Menú principal'],
        ]);

        $this->conv->setState($from, BotState::EVENTO_DETALLE->value, ['evento_id' => $eventoId]);
    }

    public function showFaqs(string $from): void
    {
        $faqs = DB::table('web_faq')
            ->where('activo', 1)
            ->orderBy('orden')
            ->limit(5)
            ->get(['id', 'pregunta', 'respuesta', 'categoria']);

        if ($faqs->isEmpty()) {
            $this->wa->sendText($from, '😔 No hay preguntas frecuentes disponibles.');
            $this->menu->handle($from);

            return;
        }

        $this->wa->sendText($from, '❓ *Preguntas Frecuentes*');

        foreach ($faqs as $faq) {
            $pregunta = mb_substr($faq->pregunta ?? '', 0, 150);
            $respuesta = mb_substr($faq->respuesta ?? '', 0, 400);
            $this->wa->sendText($from, "❓ *{$pregunta}*\n\n{$respuesta}");
        }

        $this->wa->sendButtons($from, '¿Necesitas más ayuda?', [
            ['id' => BotButton::SOPORTE->value, 'title' => '📞 Hablar con asesor'],
            ['id' => BotButton::MENU->value,     'title' => '🏠 Menú principal'],
        ]);
    }
}
