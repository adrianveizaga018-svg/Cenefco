<?php

namespace App\Application\Usuarios\Handlers;

use App\Application\Usuarios\Commands\VerifyEmailCommand;
use App\Domain\Usuarios\Contracts\UserRepositoryInterface;

class VerifyEmailHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function handle(VerifyEmailCommand $command): bool
    {
        return $this->userRepository->verifyEmail($command->userId);
    }
}
