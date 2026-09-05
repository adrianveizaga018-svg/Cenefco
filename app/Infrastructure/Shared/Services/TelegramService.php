<?php

namespace App\Infrastructure\Shared\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    private string $baseUrl;

    public function __construct()
    {
        $token = config('telegram.bot_token');
        $this->baseUrl = "https://api.telegram.org/bot{$token}";
    }

    public function sendText(int|string $chatId, string $text, string $parseMode = 'Markdown'): array
    {
        return $this->call('sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $parseMode,
        ]);
    }

    public function sendInlineKeyboard(int|string $chatId, string $text, array $buttons, string $parseMode = 'Markdown'): array
    {
        $keyboard = array_map(fn ($b) => [$b], $buttons);

        return $this->call('sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $parseMode,
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ]);
    }

    public function sendPhoto(int|string $chatId, string $photo, string $caption = ''): array
    {
        return $this->call('sendPhoto', [
            'chat_id' => $chatId,
            'photo' => $photo,
            'caption' => $caption,
            'parse_mode' => 'Markdown',
        ]);
    }

    public function sendDocument(int|string $chatId, string $document, string $caption = ''): array
    {
        return $this->call('sendDocument', [
            'chat_id' => $chatId,
            'document' => $document,
            'caption' => $caption,
            'parse_mode' => 'Markdown',
        ]);
    }

    public function sendAudio(int|string $chatId, string $audio, string $caption = ''): array
    {
        return $this->call('sendAudio', [
            'chat_id' => $chatId,
            'audio' => $audio,
            'caption' => $caption,
        ]);
    }

    public function sendVideo(int|string $chatId, string $video, string $caption = ''): array
    {
        return $this->call('sendVideo', [
            'chat_id' => $chatId,
            'video' => $video,
            'caption' => $caption,
            'parse_mode' => 'Markdown',
        ]);
    }

    public function sendLocation(int|string $chatId, float $latitude, float $longitude): array
    {
        return $this->call('sendLocation', [
            'chat_id' => $chatId,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    public function answerCallbackQuery(string $callbackQueryId, string $text = ''): array
    {
        return $this->call('answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
        ]);
    }

    public function setWebhook(string $url, string $secret = ''): array
    {
        $params = ['url' => $url];

        if ($secret) {
            $params['secret_token'] = $secret;
        }

        return $this->call('setWebhook', $params);
    }

    public function getWebhookInfo(): array
    {
        return $this->call('getWebhookInfo', []);
    }

    public function deleteWebhook(): array
    {
        return $this->call('deleteWebhook', []);
    }

    public function getMe(): array
    {
        return $this->call('getMe', []);
    }

    private function call(string $method, array $params): array
    {
        $response = Http::post("{$this->baseUrl}/{$method}", $params);

        return $response->json() ?? [];
    }
}
