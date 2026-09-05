<?php

namespace App\Application\Usuarios\Handlers;

use App\Application\Usuarios\Commands\UpdatePerfilCommand;
use App\Application\Usuarios\DTOs\UserDTO;
use App\Domain\Usuarios\Contracts\UserRepositoryInterface;

class UpdatePerfilHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function handle(UpdatePerfilCommand $command): UserDTO
    {
        return $this->userRepository->updatePerfil($command);
    }
}
