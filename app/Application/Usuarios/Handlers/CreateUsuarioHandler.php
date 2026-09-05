<?php

namespace App\Application\Usuarios\Handlers;

use App\Application\Usuarios\Commands\CreateUsuarioCommand;
use App\Application\Usuarios\DTOs\UserDTO;
use App\Domain\Usuarios\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class CreateUsuarioHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository
    ) {}

    public function handle(CreateUsuarioCommand $command): UserDTO
    {
        return $this->repository->create([
            'nombre'        => $command->nombre,
            'apellido'      => $command->apellido,
            'email'         => $command->email,
            'password_hash' => Hash::make($command->password),
            'tipo'          => $command->tipo,
            'rol_id'        => $command->rolId,
            'activo'        => $command->activo,
        ]);
    }
}
