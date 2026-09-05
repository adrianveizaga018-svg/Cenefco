<?php

namespace App\Application\Usuarios\Handlers;

use App\Application\Usuarios\Commands\RegisterUserCommand;
use App\Application\Usuarios\DTOs\UserDTO;
use App\Domain\Usuarios\Contracts\UserRepositoryInterface;

class RegisterUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function handle(RegisterUserCommand $command): UserDTO
    {
        return $this->userRepository->register([
            'nombre'   => $command->nombre,
            'apellido' => $command->apellido,
            'email'    => $command->email,
            'password' => $command->password,
            'tipo'     => 'ciudadano',
            'activo'   => true,
            'email_verificado' => false,
            'rol_id'   => $command->roleId,
        ]);
    }
}
