<?php

namespace App\Application\Usuarios\Handlers;

use App\Domain\Usuarios\Contracts\UserRepositoryInterface;

class LogoutAllHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function handle(int $userId): void
    {
        $this->userRepository->logoutAll($userId);
    }
}
