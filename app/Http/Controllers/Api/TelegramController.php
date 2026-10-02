<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Infrastructure\Shared\Services\TelegramBotService;
use App\Infrastructure\Shared\Services\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TelegramController extends Controller
{
    private TelegramService $telegram;

    private TelegramBotService $bot;

    public function __construct()
    {
        $this->telegram = new TelegramService;
        $this->bot = new TelegramBotService;
    }

    public function webhook(Request $request): JsonResponse
    {
        $secret = config('telegram.webhook_secret');
        if ($secret) {
            $header = $request->header('X-Telegram-Bot-Api-Secret-Token', '');
            if (! hash_equals($secret, $header)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $update = $request->all();

        \Log::info('Telegram update recibido', ['update_id' => $update['update_id'] ?? null]);

        $updateId = $update['update_id'] ?? null;
        if ($updateId && Cache::has("tgupd:{$updateId}")) {
            return response()->json(['status' => 'duplicate']);
        }
        if ($updateId) {
            Cache::put("tgupd:{$updateId}", true, now()->addMinutes(5));
        }

        if (isset($update['message'])) {
            $message = $update['message'];
            $chatId = $message['chat']['id'];
            $type = isset($message['text']) ? 'message' : 'other';

            if ($type === 'message') {
                \Log::info('Telegram mensaje', [
                    'chat_id' => $chatId,
                    'text' => $message['text'],
                    'from' => $message['from']['username'] ?? $message['from']['first_name'] ?? 'unknown',
                ]);

                $this->bot->handleUpdate($chatId, 'message', $update);
            }
        }

        if (isset($update['callback_query'])) {
            $callbackQuery = $update['callback_query'];
            $chatId = $callbackQuery['message']['chat']['id'];

            \Log::info('Telegram callback', [
                'chat_id' => $chatId,
                'data' => $callbackQuery['data'] ?? '',
                'from' => $callbackQuery['from']['username'] ?? $callbackQuery['from']['first_name'] ?? 'unknown',
            ]);

            $this->bot->handleUpdate($chatId, 'callback_query', $update);
        }

        return response()->json(['status' => 'ok']);
    }

    public function setWebhook(Request $request): JsonResponse
    {
        $url = $request->query('url');

        if (! $url) {
            return response()->json(['error' => 'Falta el parámetro "url"'], 422);
        }

        $secret = config('telegram.webhook_secret');
        $response = $this->telegram->setWebhook($url, $secret);

        return response()->json($response);
    }

    public function webhookInfo(): JsonResponse
    {
        return response()->json($this->telegram->getWebhookInfo());
    }

    public function deleteWebhook(): JsonResponse
    {
        return response()->json($this->telegram->deleteWebhook());
    }

    public function botTest(Request $request): JsonResponse
    {
        $chatId = $request->query('chat_id');

        if (! $chatId) {
            return response()->json(['error' => 'Falta el parámetro "chat_id"'], 422);
        }

        $this->bot->sendBienvenida($chatId);

        return response()->json(['status' => 'bot menu sent', 'chat_id' => $chatId]);
    }

    public function me(): JsonResponse
    {
        return response()->json($this->telegram->getMe());
    }
}
