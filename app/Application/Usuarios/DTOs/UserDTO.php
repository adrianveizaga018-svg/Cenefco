<?php

namespace App\Application\Usuarios\DTOs;

use App\Infrastructure\Usuarios\Models\User;

class UserDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nombre,
        public readonly string $apellido,
        public readonly string $email,
        public readonly string $tipo,
        public readonly bool $activo,
        public readonly bool $emailVerificado,
        public readonly ?int $rolId,
        public readonly ?string $rolNombre,
        public readonly ?array $permisos,
        public readonly string $createdAt,
        public readonly ?string $avatarUrl = null,
        public readonly ?string $ci = null,
        public readonly ?string $telefono = null,
    ) {}

    public static function fromModel(User $user): self
    {
        
        
        
        
        
        
        $rol = $user->roles->first();
        $permisos = $user->roles
            ->flatMap(fn ($r) => $r->permisos)
            ->pluck('codigo')
            ->unique()
            ->values()
            ->all();

        return new self(
            id: $user->id,
            nombre: $user->nombre,
            apellido: $user->apellido,
            email: $user->email,
            tipo: $user->tipo,
            activo: (bool) $user->activo,
            emailVerificado: (bool) $user->email_verificado,
            rolId: $rol?->id,
            rolNombre: $rol?->nombre,
            permisos: $permisos,
            createdAt: $user->created_at?->toIso8601String() ?? '',
            avatarUrl: $user->avatar_url,
            ci: $user->ci,
            telefono: $user->telefono,
        );
    }
}
