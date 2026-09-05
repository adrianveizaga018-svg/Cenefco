<?php

namespace App\Application\Usuarios\Handlers;

use App\Application\Usuarios\Commands\UpdateUserCommand;
use App\Application\Usuarios\DTOs\UserDTO;
use App\Domain\Usuarios\Contracts\UserRepositoryInterface;

class UpdateUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function handle(UpdateUserCommand $command): UserDTO
    {
        $data = array_filter([
            'nombre' => $command->nombre,
            'apellido' => $command->apellido,
            'email' => $command->email,
            'rol_id' => $command->roleId,
        ], fn ($v) => $v !== null);

        return $this->userRepository->update($command->id, $data);
    }
}
