<?php

namespace App\Http\Controllers\Api\Auth;

use App\Application\Usuarios\DTOs\UserDTO;
use App\Http\Controllers\Controller;
use App\Infrastructure\Usuarios\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Traspaso de sesión entre el panel y el portal público.
 *
 * Ambos frontends viven en orígenes distintos y no comparten localStorage.
 * El panel pide un código de un solo uso para el usuario autenticado y el
 * portal lo canjea por un token propio. El token del panel nunca viaja en la URL.
 */
class SesionHandoffController extends Controller
{
    private const TTL_SEGUNDOS = 60;

    public function crear(Request $request): JsonResponse
    {
        $codigo = Str::random(64);

        Cache::put($this->clave($codigo), $request->user()->id, self::TTL_SEGUNDOS);

        return response()->json([
            'code'       => $codigo,
            'portal_url' => rtrim((string) config('services.portal.url'), '/'),
        ]);
    }

    public function canjear(Request $request): JsonResponse
    {
        $request->validate(['code' => ['required', 'string', 'size:64']]);

        $userId = Cache::pull($this->clave($request->input('code')));
        $user   = $userId ? User::with('roles')->find($userId) : null;

        if (! $user || ! $user->activo) {
            return response()->json(['error' => 'El enlace de acceso expiró. Inicia sesión nuevamente.'], 401);
        }

        $expiresAt = config('sanctum.expiration')
            ? now()->addMinutes((int) config('sanctum.expiration'))
            : null;

        $newToken = $user->createToken($request->header('User-Agent', 'portal'), ['*'], $expiresAt);

        return response()->json([
            'token'      => $newToken->plainTextToken,
            'user'       => UserDTO::fromModel($user),
            'expires_at' => $expiresAt?->toIso8601String(),
        ]);
    }

    private function clave(string $codigo): string
    {
        return 'sesion_handoff:'.hash('sha256', $codigo);
    }
}
