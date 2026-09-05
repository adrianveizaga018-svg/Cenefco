<?php

namespace App\Http\Controllers\Api;

use App\Application\Pagos\Commands\IniciarPagoPortalCommand;
use App\Application\Pagos\Handlers\IniciarPagoPortalHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\IniciarPagoPublicoRequest;
use App\Infrastructure\Pagos\Models\PagoOnlineSession;
use Illuminate\Http\JsonResponse;

class PagoPublicoController extends Controller
{
    public function __construct(
        private readonly IniciarPagoPortalHandler $iniciarHandler,
    ) {}

    public function iniciar(IniciarPagoPublicoRequest $request): JsonResponse
    {
        $result = $this->iniciarHandler->handle(new IniciarPagoPortalCommand(
            idPrograma:  $request->integer('id_programa'),
            nombre:      $request->input('nombre'),
            appaterno:   $request->input('appaterno'),
            ci:          $request->input('ci'),
            expedido:    $request->input('expedido'),
            email:       $request->input('email'),
            telefono:    $request->input('telefono'),
            idFechapago: $request->filled('id_fechapago') ? $request->integer('id_fechapago') : null,
            monto:       (float) $request->input('monto'),
            successUrl:  $request->input('success_url'),
            cancelUrl:   $request->input('cancel_url'),
        ));

        return response()->json($result, 201);
    }

    public function estado(int $sessionId): JsonResponse
    {
        $sesion = PagoOnlineSession::find($sessionId);

        if (! $sesion) {
            return response()->json(['message' => 'Sesión no encontrada'], 404);
        }

        return response()->json([
            'session_id' => $sesion->id,
            'estado'     => $sesion->estado,
            'expires_at' => $sesion->expires_at,
        ]);
    }
}
