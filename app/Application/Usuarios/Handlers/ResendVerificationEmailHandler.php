<?php

namespace App\Application\Usuarios\Handlers;

use App\Application\Usuarios\Commands\ResendVerificationEmailCommand;
use App\Domain\Usuarios\Contracts\UserRepositoryInterface;

class ResendVerificationEmailHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function handle(ResendVerificationEmailCommand $command): bool
    {
        return $this->userRepository->sendVerificationEmail($command->userId);
    }
}
