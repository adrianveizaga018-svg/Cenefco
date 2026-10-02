<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificadoWebhookController extends Controller
{
    /**
     * Recibe el archivo ZIP generado por el sistema externo de certificados (Django/Celery)
     * y lo almacena en el storage de Laravel como fuente de verdad única.
     *
     * POST /api/v1/certificados/webhook-lote
     * Headers: Authorization: Bearer {CERTIFICADOS_WEBHOOK_SECRET}
     */
    public function recibirLote(Request $request): JsonResponse
    {
        // Validar token secreto compartido entre Django y Laravel
        $secret = config('certificados_externos.webhook_secret');
        if ($secret && $request->header('X-Webhook-Secret') !== $secret) {
            return response()->json(['message' => 'No autorizado.'], 401);
        }

        $request->validate([
            'lote_id'   => ['required', 'string', 'max:100'],
            'curso'     => ['required', 'string', 'max:255'],
            'total'     => ['required', 'integer', 'min:0'],
            'zip'       => ['required', 'file', 'mimes:zip', 'max:102400'], // 100MB máx
        ]);

        $loteId   = $request->input('lote_id');
        $curso    = Str::slug($request->input('curso'));
        $zipFile  = $request->file('zip');

        // Guardar en storage/app/public/certificados/lotes/{lote_id}/
        $path = $zipFile->storeAs(
            "certificados/lotes/{$loteId}",
            "{$curso}_{$loteId}.zip",
            'public'
        );

        return response()->json([
            'message'    => 'Lote recibido y almacenado correctamente.',
            'archivo'    => $path,
            'url_publica' => Storage::url($path),
            'lote_id'   => $loteId,
            'total'     => $request->integer('total'),
        ], 201);
    }
}
