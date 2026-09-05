<?php

namespace App\Application\Usuarios\Handlers;

use App\Application\Usuarios\Commands\LoginCommand;
use App\Domain\Usuarios\Contracts\UserRepositoryInterface;

class LoginHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function handle(LoginCommand $command): array
    {
        return $this->userRepository->login($command);
    }
}
