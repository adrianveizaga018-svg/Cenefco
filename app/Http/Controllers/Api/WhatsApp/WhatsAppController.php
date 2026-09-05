<?php

namespace App\Http\Controllers\Api\WhatsApp;

use App\Http\Controllers\Controller;
use App\Infrastructure\Shared\Services\WhatsAppBotService;
use App\Infrastructure\Shared\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    public function __construct(
        private WhatsAppBotService $bot,
        private WhatsAppService $wa,
    ) {}

    public function verify(Request $request): Response|JsonResponse
    {
        $mode      = $request->query('hub_mode');
        $token     = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === config('whatsapp.verify_token')) {
            return response($challenge, 200);
        }

        return response()->json(['error' => 'Verification failed'], 403);
    }

    public function receive(Request $request): JsonResponse
    {
        $appSecret = config('whatsapp.app_secret');

        if (empty($appSecret)) {
            if (app()->environment('production')) {
                Log::error('WhatsApp WHATSAPP_APP_SECRET no configurado en producción — webhook rechazado por seguridad.');

                return response()->json(['error' => 'Webhook not configured'], 503);
            }
            Log::warning('WhatsApp app_secret no configurado — webhook sin verificación de firma (solo permitido fuera de producción)');
        } else {
            $signature = $request->header('X-Hub-Signature-256', '');
            $expected  = 'sha256='.hash_hmac('sha256', $request->getContent(), $appSecret);

            if (! hash_equals($expected, $signature)) {
                Log::warning('WhatsApp webhook: firma inválida rechazada', ['ip' => $request->ip()]);

                return response()->json(['error' => 'Invalid signature'], 403);
            }
        }

        $payload = $request->all();

        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $value = $change['value'] ?? [];

                if (isset($value['statuses'])) {
                    continue;
                }

                $nombre = $value['contacts'][0]['profile']['name'] ?? null;

                foreach ($value['messages'] ?? [] as $message) {
                    $messageId = $message['id'] ?? null;

                    try {
                        if ($messageId && ! Cache::add("wamsg:{$messageId}", true, 300)) {
                            continue;
                        }
                    } catch (\Throwable) {

                    }

                    $from = $message['from'];
                    $type = $message['type'];

                    Log::info('WhatsApp mensaje recibido', [
                        'id'     => $messageId,
                        'from'   => $from,
                        'nombre' => $nombre,
                        'type'   => $type,
                        'body'   => $message['text']['body'] ?? '[no text]',
                    ]);

                    $this->wa->sendTyping($from, $messageId);

                    try {
                        $this->bot->handleMessage($from, $type, $message, $nombre);
                    } catch (\Throwable $e) {
                        Log::error('[WhatsApp] Error en handleMessage', [
                            'from'  => $from,
                            'error' => $e->getMessage(),
                            'file'  => $e->getFile(),
                            'line'  => $e->getLine(),
                        ]);
                    }
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    public function test(): JsonResponse
    {
        try {
            $profile = $this->wa->getBusinessProfile();

            return response()->json([
                'status'  => 'ok',
                'message' => 'Conexión con WhatsApp Cloud API exitosa.',
                'profile' => $profile,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function botTest(Request $request): JsonResponse
    {
        $phone = $request->query('phone');

        if (! $phone) {
            return response()->json(['error' => 'Parámetro "phone" requerido. Ejemplo: ?phone=59176000000'], 422);
        }

        try {
            $this->bot->sendBienvenida($phone);

            return response()->json([
                'status'  => 'ok',
                'message' => "Mensaje de bienvenida enviado a {$phone}.",
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
