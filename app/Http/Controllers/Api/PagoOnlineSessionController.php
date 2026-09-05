<?php

namespace App\Http\Controllers\Api;

use App\Domain\Pagos\Contracts\PagoRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Infrastructure\Inscripciones\Models\Inscripcion;
use App\Infrastructure\Pagos\Models\PagoOnlineSession;
use App\Infrastructure\Pasarelas\PagosYaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoOnlineSessionController extends Controller
{
    public function __construct(
        private readonly PagosYaService         $pagosYa,
        private readonly PagoRepositoryInterface $pagoRepository,
    ) {}

    public function iniciar(Request $request): JsonResponse
    {
        $request->validate([
            'id_ins'       => ['required', 'integer'],
            'monto'        => ['required', 'numeric', 'min:0.01'],
            'success_url'  => ['required', 'url'],
            'cancel_url'   => ['required', 'url'],
            'id_fechapago' => ['nullable', 'integer'],
        ]);

        $idIns      = (int) $request->id_ins;
        $idFechapago = $request->id_fechapago ? (int) $request->id_fechapago : null;
        $monto      = (float) $request->monto;
        $orderId    = 'cenefco-' . $idIns . '-' . time();

        $inscripcion = Inscripcion::find($idIns);
        if (! $inscripcion) {
            return response()->json(['message' => 'Inscripción no encontrada.'], 404);
        }

        $checkout = $this->pagosYa->crearCheckout([
            'order_id'    => $orderId,
            'amount'      => $monto,
            'currency'    => 'BOB',
            'success_url' => $request->success_url,
            'cancel_url'  => $request->cancel_url,
            'customer'    => ['name' => '', 'email' => ''],
        ]);

        $session = PagoOnlineSession::create([
            'id_ins'       => $idIns,
            'id_fechapago' => $idFechapago,
            'proveedor'    => 'pagosya',
            'checkout_id'  => $checkout->checkout_id,
            'reference'    => $checkout->reference,
            'checkout_url' => $checkout->checkout_url,
            'order_id'     => $orderId,
            'monto'        => $monto,
            'moneda'       => 'BOB',
            'estado'       => 'pendiente',
            'expires_at'   => now()->addHours(2),
        ]);

        return response()->json([
            'session_id'   => $session->id,
            'checkout_url' => $session->checkout_url,
        ], 201);
    }

    public function estado(int $sessionId): JsonResponse
    {
        $session = PagoOnlineSession::find($sessionId);
        if (! $session) {
            return response()->json(['message' => 'Sesión no encontrada.'], 404);
        }

        $idPago = null;
        if ($session->estado === 'pagado' && $session->transaction_id) {
            $pago = DB::table('t_pago')
                ->where('nro_boleta_bancaria', $session->transaction_id)
                ->where('estado', 1)
                ->value('id_pago');
            $idPago = $pago ? (int) $pago : null;
        }

        return response()->json([
            'estado'  => $session->estado,
            'id_pago' => $idPago,
        ]);
    }

    public function webhook(Request $request): JsonResponse
    {
        $rawBody = $request->getContent();
        $firma   = $request->header('X-PagosYa-Signature', '');

        if (! $this->pagosYa->verificarFirmaWebhook($rawBody, $firma)) {
            return response()->json(['message' => 'Firma inválida.'], 401);
        }

        $payload = $request->input();
        $evento  = $payload['event'] ?? null;

        if ($evento !== 'checkout.completed') {
            return response()->json(['received' => true]);
        }

        $data          = $payload['data'] ?? [];
        $checkoutId    = $data['checkout_id'] ?? null;
        $transactionId = $data['transaction_id'] ?? null;
        $saleId        = $data['sale_id'] ?? null;
        $monto         = (float) ($data['amount'] ?? 0);

        if (! $checkoutId) {
            return response()->json(['message' => 'checkout_id faltante.'], 422);
        }

        $session = PagoOnlineSession::where('checkout_id', $checkoutId)->first();
        if (! $session) {
            return response()->json(['message' => 'Sesión no encontrada.'], 404);
        }

        if ($session->estado === 'pagado') {
            return response()->json(['received' => true]);
        }

        if ($transactionId && $this->pagoRepository->existePorBoleta($transactionId)) {
            return response()->json(['received' => true]);
        }

        $idUs = DB::table('t_inscripcion')
            ->where('id_ins', $session->id_ins)
            ->value('id_us');

        if (! $idUs) {
            \Log::error('[webhook] Inscripción sin usuario válido', [
                'id_ins'      => $session->id_ins,
                'checkout_id' => $checkoutId,
            ]);
            return response()->json(['message' => 'Inscripción inválida.'], 422);
        }

        DB::transaction(function () use ($session, $transactionId, $saleId, $monto, $payload, $idUs) {
            $this->pagoRepository->create([
                'id_us_reg'           => 0,
                'id_us'               => $idUs,
                'id_mat'              => null,
                'id_fechapago'        => $session->id_fechapago,
                'monto_pagado'        => $monto,
                'nro_boleta_bancaria' => $transactionId,
                'fecha_deposito'      => now()->toDateString(),
                'metodo_pago'         => 'pago_online',
                'id_us_cajero'        => null,
                'pago_extra'          => $session->id_fechapago ? 0 : 1,
                'estado'              => 1,
                'fecha_reg'           => now(),
            ]);

            $session->update([
                'estado'          => 'pagado',
                'transaction_id'  => $transactionId,
                'sale_id'         => $saleId,
                'webhook_payload' => $payload,
            ]);
        });

        return response()->json(['received' => true]);
    }
}
