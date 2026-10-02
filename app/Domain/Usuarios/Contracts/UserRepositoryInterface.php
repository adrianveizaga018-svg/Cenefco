<?php

namespace App\Domain\Usuarios\Contracts;

use App\Application\Usuarios\Commands\LoginCommand;
use App\Application\Usuarios\Commands\LoginGoogleCommand;
use App\Application\Usuarios\Commands\UpdatePerfilCommand;
use App\Application\Usuarios\DTOs\UserDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface UserRepositoryInterface
{
    public function findById(int $id): UserDTO;

    public function findByEmail(string $email): ?UserDTO;

    public function paginate(PaginationDTO $pagination, array $filters = []): array;

    public function create(array $data): UserDTO;

    public function update(int $id, array $data): UserDTO;

    public function delete(int $id): void;

    public function login(LoginCommand $command): array;

    public function loginWithGoogle(LoginGoogleCommand $command): array;

    public function logout(int $userId): void;

    public function logoutAll(int $userId): void;

    public function getWithRoles(int $id): UserDTO;

    public function register(array $data): UserDTO;

    public function updatePerfil(UpdatePerfilCommand $command): UserDTO;

    public function verifyEmail(int $id): bool;

    public function sendVerificationEmail(int $id): bool;
}
