<?php

namespace App\Http\Controllers\Api\Auth;

use App\Application\Usuarios\Commands\ResendVerificationEmailCommand;
use App\Application\Usuarios\Commands\VerifyEmailCommand;
use App\Application\Usuarios\Handlers\ResendVerificationEmailHandler;
use App\Application\Usuarios\Handlers\VerifyEmailHandler;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function __construct(
        private readonly VerifyEmailHandler $verifyEmailHandler,
        private readonly ResendVerificationEmailHandler $resendVerificationEmailHandler,
    ) {}

    public function verify(Request $request): JsonResponse
    {
        $verified = $this->verifyEmailHandler->handle(
            new VerifyEmailCommand(userId: $request->user()->id)
        );

        $message = $verified ? 'Email verificado exitosamente.' : 'Email ya verificado.';

        return response()->json(['message' => $message]);
    }

    public function resend(Request $request): JsonResponse
    {
        $sent = $this->resendVerificationEmailHandler->handle(
            new ResendVerificationEmailCommand(userId: $request->user()->id)
        );

        $message = $sent ? 'Email de verificación reenviado.' : 'Email ya verificado.';

        return response()->json(['message' => $message]);
    }
}
