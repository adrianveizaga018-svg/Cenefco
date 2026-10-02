<?php

namespace App\Infrastructure\Shared\Services;

class TelegramBotService
{
    private TelegramService $tg;

    public function __construct()
    {
        $this->tg = new TelegramService;
    }

    public function handleUpdate(int|string $chatId, string $type, array $update): void
    {
        if ($type === 'message') {
            $text = strtolower($this->removeAccents($update['message']['text'] ?? ''));
            $this->routeByKeyword($chatId, $text);

            return;
        }

        if ($type === 'callback_query') {
            $callbackId = $update['callback_query']['id'];
            $data = $update['callback_query']['data'] ?? '';

            $this->tg->answerCallbackQuery($callbackId);
            $this->routeByCallbackData($chatId, $data);
        }
    }

    private function routeByKeyword(int|string $chatId, string $text): void
    {
        $keywords = config('bot.keywords');

        foreach ($keywords as $intent => $words) {
            foreach ($words as $word) {
                if (str_contains($text, $word)) {
                    $this->dispatch($chatId, $intent);

                    return;
                }
            }
        }

        $this->sendMenu($chatId);
    }

    private function routeByCallbackData(int|string $chatId, string $data): void
    {
        match ($data) {
            'btn_catalogo' => $this->sendCatalogo($chatId),
            'btn_horario' => $this->sendHorario($chatId),
            'btn_pedido' => $this->sendPedido($chatId),
            'btn_ubicacion' => $this->sendUbicacion($chatId),
            default => $this->sendMenu($chatId),
        };
    }

    private function dispatch(int|string $chatId, string $intent): void
    {
        match ($intent) {
            'saludo' => $this->sendBienvenida($chatId),
            'catalogo' => $this->sendCatalogo($chatId),
            'horario' => $this->sendHorario($chatId),
            'pedido' => $this->sendPedido($chatId),
            'ubicacion' => $this->sendUbicacion($chatId),
            default => $this->sendMenu($chatId),
        };
    }

    public function sendBienvenida(int|string $chatId): void
    {
        $negocio = config('bot.negocio.nombre');
        $this->tg->sendText(
            $chatId,
            "¡Hola! Bienvenido/a a *{$negocio}*.\n\nEstoy aquí para ayudarte. ¿En qué puedo asistirte hoy?"
        );
        $this->sendMenu($chatId);
    }

    public function sendMenu(int|string $chatId): void
    {
        $this->tg->sendInlineKeyboard(
            $chatId,
            '¿Qué necesitas? Elige una opción:',
            [
                ['text' => '🛍️ Catálogo',      'callback_data' => 'btn_catalogo'],
                ['text' => '🕐 Horarios',        'callback_data' => 'btn_horario'],
                ['text' => '📦 Hacer pedido',    'callback_data' => 'btn_pedido'],
                ['text' => '📍 Nuestra ubicación', 'callback_data' => 'btn_ubicacion'],
            ]
        );
    }

    public function sendCatalogo(int|string $chatId): void
    {
        $productos = config('bot.productos');
        $texto = "🛍️ *Nuestro Catálogo*\n\n";

        foreach ($productos as $i => $p) {
            $num = $i + 1;
            $texto .= "*{$num}. {$p['nombre']}*\n";
            $texto .= "💰 Precio: {$p['precio']}\n";
            $texto .= "📝 {$p['descripcion']}\n\n";
        }

        $texto .= 'Para hacer un pedido escribe *pedido* o el nombre del producto que deseas.';
        $this->tg->sendText($chatId, $texto);

        foreach ($productos as $p) {
            if (! empty($p['imagen_url'])) {
                $this->tg->sendPhoto($chatId, $p['imagen_url'], $p['nombre']);
            }
        }
    }

    public function sendHorario(int|string $chatId): void
    {
        $horarios = config('bot.negocio.horarios');
        $negocio = config('bot.negocio.nombre');
        $texto = "🕐 *Horarios de atención - {$negocio}*\n\n";

        foreach ($horarios as $dia => $hora) {
            $texto .= "📅 *{$dia}:* {$hora}\n";
        }

        $texto .= "\n¿Necesitas algo más?";
        $this->tg->sendText($chatId, $texto);
        $this->sendMenu($chatId);
    }

    public function sendPedido(int|string $chatId): void
    {
        $pasos = config('bot.pasos_pedido');
        $texto = "📦 *¿Cómo hacer tu pedido?*\n\n";

        foreach ($pasos as $i => $paso) {
            $num = $i + 1;
            $texto .= "{$num}️⃣ {$paso}\n";
        }

        $contacto = config('bot.negocio.contacto.telefono');
        $texto .= "\n📲 También puedes llamarnos: {$contacto}";

        $this->tg->sendText($chatId, $texto);
        $this->sendMenu($chatId);
    }

    public function sendUbicacion(int|string $chatId): void
    {
        $loc = config('bot.negocio.ubicacion');

        $this->tg->sendLocation($chatId, $loc['latitude'], $loc['longitude']);

        $maps = $loc['maps_link'];
        $this->tg->sendText(
            $chatId,
            "📍 *Dirección:* {$loc['direccion']}\n🏷️ *Referencia:* {$loc['referencia']}\n\n🗺️ [Ver en Maps]({$maps})"
        );
    }

    private function removeAccents(string $text): string
    {
        return strtr($text, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u', 'ñ' => 'n', 'Ñ' => 'n',
        ]);
    }
}
