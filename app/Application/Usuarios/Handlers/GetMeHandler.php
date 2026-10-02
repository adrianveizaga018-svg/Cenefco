<?php

namespace App\Application\Usuarios\Handlers;

use App\Application\Usuarios\DTOs\UserDTO;
use App\Domain\Usuarios\Contracts\UserRepositoryInterface;

class GetMeHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function handle(int $userId): UserDTO
    {
        return $this->userRepository->getWithRoles($userId);
    }
}
