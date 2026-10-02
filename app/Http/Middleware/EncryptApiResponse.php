<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EncryptApiResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->debecifrarse($response)) {
            return $response;
        }

        $keyHex = config('services.api_encrypt.key');

        if (empty($keyHex)) {
            return $response;
        }

        $keyBin = hex2bin($keyHex);
        $iv = random_bytes(16);

        $ciphertext = openssl_encrypt(
            $response->getContent(),
            'AES-256-CBC',
            $keyBin,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($ciphertext === false) {
            return $response;
        }

        $response->setContent(json_encode([
            'encrypted' => base64_encode($ciphertext),
            'iv' => bin2hex($iv),
        ]));

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('X-Encrypted', '1');

        return $response;
    }

    private function debecifrarse(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');

        return str_contains($contentType, 'application/json')
            && $response->getStatusCode() >= 200
            && $response->getStatusCode() < 300;
    }
}
