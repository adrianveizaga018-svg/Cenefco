<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class WhatsAppBotProcessController extends Controller
{
    private string $nodeServiceUrl;

    public function __construct()
    {
        $this->nodeServiceUrl = env('WHATSAPP_SERVICE_URL', 'http://localhost:3001');
    }

    public function processStatus(): JsonResponse
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(3)
                ->get("{$this->nodeServiceUrl}/health");

            $running = $response->successful() && ($response->json('ok') === true);

            return response()->json(['running' => $running, 'pid' => null]);
        } catch (\Throwable) {
            return response()->json(['running' => false, 'pid' => null]);
        }
    }

    public function start(): JsonResponse
    {
        return response()->json([
            'message' => 'El servicio Node.js se gestiona externamente con PM2. Conéctate al VPS para iniciarlo.',
            'running' => false,
        ], 501);
    }

    public function stop(): JsonResponse
    {
        return response()->json([
            'message' => 'El servicio Node.js se gestiona externamente con PM2. Conéctate al VPS para detenerlo.',
            'running' => true,
        ], 501);
    }

    public function logs(): JsonResponse
    {
        return response()->json([
            'logs' => '// Los logs del bot se consultan con "pm2 logs cenefco-bot" en el VPS.',
        ]);
    }

    public function flushCache(): JsonResponse
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->post("{$this->nodeServiceUrl}/cache/flush");

            if ($response->successful()) {
                return response()->json(['message' => 'Caché del bot invalidada correctamente.']);
            }
            return response()->json(['message' => 'El bot no respondió.'], 502);
        } catch (\Throwable) {
            return response()->json(['message' => 'No se pudo conectar al servicio Node.js.'], 503);
        }
    }

    public function connectActive(): JsonResponse
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->post("{$this->nodeServiceUrl}/connect-active");

            if ($response->successful()) {
                return response()->json(['message' => 'Iniciando conexión WhatsApp…']);
            }
            return response()->json(['message' => 'El bot no respondió.'], 502);
        } catch (\Throwable) {
            return response()->json(['message' => 'No se pudo conectar al servicio Node.js.'], 503);
        }
    }

    public function disconnect(): JsonResponse
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->post("{$this->nodeServiceUrl}/disconnect");

            if ($response->successful()) {
                return response()->json(['message' => 'WhatsApp desvinculado.']);
            }
            return response()->json(['message' => 'El bot no respondió.'], 502);
        } catch (\Throwable) {
            return response()->json(['message' => 'No se pudo conectar al servicio Node.js.'], 503);
        }
    }
}
