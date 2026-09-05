<?php

namespace App\Application\Usuarios\Handlers;

use App\Application\Usuarios\Commands\LoginGoogleCommand;
use App\Domain\Usuarios\Contracts\UserRepositoryInterface;

class LoginGoogleHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function handle(LoginGoogleCommand $command): array
    {
        return $this->userRepository->loginWithGoogle($command);
    }
}
