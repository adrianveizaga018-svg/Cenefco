<?php

namespace App\Application\Usuarios\Handlers;

use App\Domain\Usuarios\Contracts\UserRepositoryInterface;

class DeleteUserHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function handle(int $id): void
    {
        $this->userRepository->delete($id);
    }
}
