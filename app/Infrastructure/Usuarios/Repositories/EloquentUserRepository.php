<?php

namespace App\Infrastructure\Usuarios\Repositories;

use App\Application\Usuarios\Commands\LoginCommand;
use App\Application\Usuarios\Commands\LoginGoogleCommand;
use App\Application\Usuarios\Commands\UpdatePerfilCommand;
use App\Application\Usuarios\DTOs\UserDTO;
use App\Domain\Usuarios\Contracts\UserRepositoryInterface;
use App\Domain\Usuarios\Exceptions\EmailDuplicadoException;
use App\Domain\Usuarios\Exceptions\UserNotFoundException;
use App\Infrastructure\Usuarios\Models\Role;
use App\Infrastructure\Usuarios\Models\User;
use App\Shared\Kernel\DTOs\PaginationDTO;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(int $id): UserDTO
    {
        $user = User::with('roles')->find($id);

        if (! $user) {
            throw new UserNotFoundException($id);
        }

        return UserDTO::fromModel($user);
    }

    public function findByEmail(string $email): ?UserDTO
    {
        $user = User::with('roles')->where('email', $email)->first();

        return $user ? UserDTO::fromModel($user) : null;
    }

    public function paginate(PaginationDTO $pagination, array $filters = []): array
    {
        $q = User::with('roles');

        if (($filters['origen'] ?? null) === 'portal') {
            $q->where('tipo', 'ciudadano');
        } elseif (($filters['origen'] ?? null) === 'sistema') {
            $q->where('tipo', '!=', 'ciudadano');
        }

        if ($pagination->query) {
            $search = $pagination->query;
            $q->where(fn ($sub) => $sub
                ->where('nombre', 'like', "%{$search}%")
                ->orWhere('apellido', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            );
        }

        $paginated = $q->orderByDesc('created_at')
            ->paginate($pagination->pageSize, ['*'], 'page', $pagination->pageIndex);

        return [
            'data'  => collect($paginated->items())->map(fn ($u) => UserDTO::fromModel($u))->all(),
            'total' => $paginated->total(),
        ];
    }

    public function create(array $data): UserDTO
    {
        if (User::where('email', $data['email'])->exists()) {
            throw new EmailDuplicadoException($data['email']);
        }

        $rolId = $data['rol_id'] ?? null;
        unset($data['rol_id']);

        $user = User::create($data);

        if ($rolId) {
            $user->roles()->attach($rolId, [
                'asignado_at'  => now(),
                'asignado_por' => $user->id,
            ]);
        }

        return UserDTO::fromModel($user->load('roles'));
    }

    public function update(int $id, array $data): UserDTO
    {
        $user = User::with('roles')->find($id);

        if (! $user) {
            throw new UserNotFoundException($id);
        }

        if (isset($data['email']) && $data['email'] !== $user->email) {
            if (User::where('email', $data['email'])->exists()) {
                throw new EmailDuplicadoException($data['email']);
            }
        }

        $rolId = $data['rol_id'] ?? null;
        unset($data['rol_id']);

        $user->update($data);

        if ($rolId) {
            $user->roles()->sync([$rolId => [
                'asignado_at'  => now(),
                'asignado_por' => auth()->id() ?? $id,
            ]]);
        }

        return UserDTO::fromModel($user->fresh('roles'));
    }

    public function delete(int $id): void
    {
        $user = User::find($id);

        if (! $user) {
            throw new UserNotFoundException($id);
        }

        $user->tokens()->delete();
        $user->delete();
    }

    public function login(LoginCommand $command): array
    {
        $user = User::with('roles')->where('email', $command->email)->first();

        if (! $user || ! Hash::check($command->password, $user->password_hash)) {
            throw new \RuntimeException('Credenciales incorrectas.');
        }

        if (! $user->activo) {
            throw new \RuntimeException('Tu cuenta está desactivada.');
        }

        $expiresAt = config('sanctum.expiration')
            ? now()->addMinutes((int) config('sanctum.expiration'))
            : null;

        $newToken = $user->createToken($command->deviceName, ['*'], $expiresAt);

        return [
            'token'      => $newToken->plainTextToken,
            'user'       => UserDTO::fromModel($user),
            'expires_at' => $expiresAt?->toIso8601String(),
        ];
    }

    public function loginWithGoogle(LoginGoogleCommand $command): array
    {
        try {
            $client = new \Google_Client(['client_id' => config('services.google.client_id')]);
            $payload = $client->verifyIdToken($command->idToken);
        } catch (\Throwable) {
            $payload = false;
        }

        if (! $payload) {
            throw new \RuntimeException('Token de Google inválido.');
        }

        $user = User::with('roles')->where('google_id', $payload['sub'])->first();

        if (! $user) {
            $user = User::with('roles')->where('email', $payload['email'])->first();

            if ($user) {
                $user->update(['google_id' => $payload['sub']]);
            }
        }

        if (! $user) {
            $user = User::create([
                'nombre'           => $payload['given_name'] ?? ($payload['name'] ?? 'Usuario'),
                'apellido'         => $payload['family_name'] ?? '',
                'email'            => $payload['email'],
                'password_hash'    => Hash::make(Str::random(40)),
                'tipo'             => 'ciudadano',
                'activo'           => true,
                'email_verificado' => true,
                'google_id'        => $payload['sub'],
                'avatar_url'       => $payload['picture'] ?? null,
            ]);

            $rolId = Role::where('nombre', 'participante')->value('id');
            if ($rolId) {
                $user->roles()->attach($rolId, [
                    'asignado_at'  => now(),
                    'asignado_por' => $user->id,
                ]);
            }

            $user->load('roles');
        }

        if (! $user->activo) {
            throw new \RuntimeException('Tu cuenta está desactivada.');
        }

        $expiresAt = config('sanctum.expiration')
            ? now()->addMinutes((int) config('sanctum.expiration'))
            : null;

        $newToken = $user->createToken($command->deviceName, ['*'], $expiresAt);

        return [
            'token'                      => $newToken->plainTextToken,
            'user'                       => UserDTO::fromModel($user),
            'expires_at'                 => $expiresAt?->toIso8601String(),
            'require_profile_completion' => empty($user->ci),
        ];
    }

    public function logout(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->currentAccessToken()->delete();
    }

    public function logoutAll(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->tokens()->delete();
    }

    public function getWithRoles(int $id): UserDTO
    {
        $user = User::with('roles')->find($id);

        if (! $user) {
            throw new UserNotFoundException($id);
        }

        return UserDTO::fromModel($user);
    }

    public function register(array $data): UserDTO
    {
        $rolId = $data['rol_id'] ?? Role::where('nombre', 'participante')->value('id');
        unset($data['rol_id']);

        $data['password_hash'] = Hash::make($data['password']);
        unset($data['password']);

        if (User::where('email', $data['email'])->exists()) {
            throw new EmailDuplicadoException($data['email']);
        }

        $user = User::create($data);

        if ($rolId) {
            $user->roles()->attach($rolId, [
                'asignado_at'  => now(),
                'asignado_por' => $user->id,
            ]);
        }

        $user->load('roles');

        event(new Registered($user));

        return UserDTO::fromModel($user);
    }

    public function updatePerfil(UpdatePerfilCommand $command): UserDTO
    {
        $user = User::with('roles')->findOrFail($command->userId);

        if ($command->newPassword !== null) {
            if (! Hash::check($command->currentPassword, $user->password_hash)) {
                throw new \RuntimeException('Contraseña actual incorrecta.');
            }
        }

        $data = array_filter([
            'nombre'        => $command->nombre,
            'apellido'      => $command->apellido,
            'ci'            => $command->ci,
            'telefono'      => $command->telefono,
            'password_hash' => $command->newPassword ? Hash::make($command->newPassword) : null,
        ], fn ($v) => $v !== null);

        $user->update($data);

        return UserDTO::fromModel($user->fresh('roles'));
    }

    public function verifyEmail(int $id): bool
    {
        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return false;
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return true;
    }

    public function sendVerificationEmail(int $id): bool
    {
        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return false;
        }

        $user->sendEmailVerificationNotification();

        return true;
    }
}
