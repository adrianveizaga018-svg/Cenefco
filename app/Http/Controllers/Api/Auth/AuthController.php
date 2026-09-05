<?php

namespace App\Http\Controllers\Api\Auth;

use App\Application\Usuarios\Commands\ForgotPasswordCommand;
use App\Application\Usuarios\Commands\LoginCommand;
use App\Application\Usuarios\Commands\LoginGoogleCommand;
use App\Application\Usuarios\Commands\RegisterUserCommand;
use App\Application\Usuarios\Commands\ResetPasswordCommand;
use App\Application\Usuarios\Commands\UpdatePerfilCommand;
use App\Application\Usuarios\Handlers\ForgotPasswordHandler;
use App\Application\Usuarios\Handlers\GetMeHandler;
use App\Application\Usuarios\Handlers\LoginGoogleHandler;
use App\Application\Usuarios\Handlers\LoginHandler;
use App\Application\Usuarios\Handlers\LogoutAllHandler;
use App\Application\Usuarios\Handlers\LogoutHandler;
use App\Application\Usuarios\Handlers\RegisterUserHandler;
use App\Application\Usuarios\Handlers\ResetPasswordHandler;
use App\Application\Usuarios\Handlers\UpdatePerfilHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Usuarios\ForgotPasswordRequest;
use App\Http\Requests\Usuarios\LoginRequest;
use App\Http\Requests\Usuarios\RegisterPublicoRequest;
use App\Http\Requests\Usuarios\RegisterUserRequest;
use App\Http\Requests\Usuarios\ResetPasswordRequest;
use App\Http\Requests\Usuarios\UpdatePerfilRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly LoginHandler $loginHandler,
        private readonly LoginGoogleHandler $loginGoogleHandler,
        private readonly LogoutHandler $logoutHandler,
        private readonly LogoutAllHandler $logoutAllHandler,
        private readonly GetMeHandler $getMeHandler,
        private readonly RegisterUserHandler $registerHandler,
        private readonly ForgotPasswordHandler $forgotPasswordHandler,
        private readonly ResetPasswordHandler $resetPasswordHandler,
        private readonly UpdatePerfilHandler $updatePerfilHandler,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->loginHandler->handle(
                new LoginCommand(
                    email: $request->email,
                    password: $request->password,
                    deviceName: $request->header('User-Agent', 'web'),
                )
            );

            return response()->json($result);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $this->logoutHandler->handle($request->user()->id);

        return response()->json(['message' => 'Sesión cerrada.']);
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $this->logoutAllHandler->handle($request->user()->id);

        return response()->json(['message' => 'Todas las sesiones cerradas.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(
            $this->getMeHandler->handle($request->user()->id)
        );
    }

    public function updatePerfil(UpdatePerfilRequest $request): JsonResponse
    {
        try {
            $userDTO = $this->updatePerfilHandler->handle(
                new UpdatePerfilCommand(
                    userId: $request->user()->id,
                    nombre: $request->nombre,
                    apellido: $request->apellido,
                    ci: $request->ci,
                    telefono: $request->telefono,
                    currentPassword: $request->current_password,
                    newPassword: $request->password,
                )
            );

            return response()->json($userDTO);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function register(RegisterUserRequest $request): JsonResponse
    {
        try {
            $userDTO = $this->registerHandler->handle(
                new RegisterUserCommand(
                    nombre: $request->nombre,
                    apellido: $request->apellido,
                    email: $request->email,
                    password: $request->password,
                    roleId: $request->rol_id,
                )
            );

            return response()->json($userDTO, 201);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function registerPublico(RegisterPublicoRequest $request): JsonResponse
    {
        try {
            // Asignar automáticamente el rol 'participante' (lead/visitante)
            // Se convertirá en 'estudiante' cuando concrete una inscripción con pago.
            $rolParticipante = \DB::table('roles')->where('nombre', 'participante')->value('id');

            $this->registerHandler->handle(
                new RegisterUserCommand(
                    nombre: $request->nombre,
                    apellido: $request->apellido,
                    email: $request->email,
                    password: $request->password,
                    roleId: $rolParticipante,
                )
            );

            $result = $this->loginHandler->handle(
                new LoginCommand(
                    email: $request->email,
                    password: $request->password,
                    deviceName: $request->header('User-Agent', 'web'),
                )
            );

            return response()->json($result, 201);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function loginGoogle(Request $request): JsonResponse
    {
        $request->validate(['id_token' => ['required', 'string']]);

        try {
            $result = $this->loginGoogleHandler->handle(
                new LoginGoogleCommand(
                    idToken: $request->id_token,
                    deviceName: $request->header('User-Agent', 'web'),
                )
            );

            return response()->json($result);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $this->forgotPasswordHandler->handle(
                new ForgotPasswordCommand($request->email)
            );

            return response()->json(['message' => 'Te enviamos un enlace de recuperación a tu correo.']);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => 'No encontramos una cuenta con ese correo electrónico.'], 404);
        }
    }
    public function completeProfile(Request $request): JsonResponse
    {
        $request->validate([
            'ci' => ['required', 'string', 'max:20'],
            'telefono' => ['required', 'string', 'max:20'],
        ]);

        $user = $request->user();
        
        $user->ci = $request->ci;
        $user->telefono = $request->telefono;
        $user->save();

        $legacyUser = \Illuminate\Support\Facades\DB::table('t_usuario')->where('ci', $request->ci)->first();
        
        if (!$legacyUser) {
            $maxId = \Illuminate\Support\Facades\DB::table('t_usuario')->max('id_us');
            $newId = $maxId ? $maxId + 1 : 1;

            \Illuminate\Support\Facades\DB::table('t_usuario')->insert([
                'id_us' => $newId,
                'nombre' => $user->nombre,
                'appaterno' => $user->apellido,
                'ci' => $request->ci,
                'celular' => $request->telefono,
                'email' => $user->email,
                'password' => $user->password_hash,
                'estado' => 1,
                'fecha_reg' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Perfil completado exitosamente.',
            'user' => \App\Application\Usuarios\DTOs\UserDTO::fromModel($user),
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $this->resetPasswordHandler->handle(new ResetPasswordCommand(
                email: $request->email,
                password: $request->password,
                passwordConfirmation: $request->password_confirmation,
                token: $request->token,
            ));

            return response()->json(['message' => 'Contraseña restablecida exitosamente.']);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
