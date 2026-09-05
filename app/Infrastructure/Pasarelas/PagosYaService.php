<?php

namespace App\Infrastructure\Pasarelas;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PagosYaService
{
    private string $baseUrl = 'https://nbjwpakpimrqfocsxkda.supabase.co/functions/v1';

    public function crearCheckout(array $datos): object
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('pagosya.api_key'),
            'Content-Type'  => 'application/json',
        ])->post("{$this->baseUrl}/create-external-checkout", $datos);

        if ($response->failed()) {
            throw new RuntimeException(
                'PagosYa checkout error: ' . $response->status() . ' — ' . $response->body()
            );
        }

        return $response->object();
    }

    public function verificarFirmaWebhook(string $rawBody, string $firma): bool
    {
        $secret = config('pagosya.webhook_secret', '');

        if ($secret === '') {
            \Log::error('[PagosYa] webhook_secret no configurado — rechazando webhook.');
            return false;
        }

        $expected = hash_hmac('sha256', $rawBody, $secret);

        return hash_equals($expected, $firma);
    }
}
