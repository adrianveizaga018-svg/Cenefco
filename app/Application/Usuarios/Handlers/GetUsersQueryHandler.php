<?php

namespace App\Application\Usuarios\Handlers;

use App\Domain\Usuarios\Contracts\UserRepositoryInterface;
use App\Shared\Kernel\DTOs\PaginationDTO;

class GetUsersQueryHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function handle(PaginationDTO $pagination): array
    {
        return $this->userRepository->paginate($pagination);
    }
}
