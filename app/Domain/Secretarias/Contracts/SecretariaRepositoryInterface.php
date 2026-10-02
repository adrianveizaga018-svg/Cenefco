<?php

namespace App\Domain\Secretarias\Contracts;

use App\Application\Secretarias\DTOs\SecretariaDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface SecretariaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array;

    public function findById(int $id): SecretariaDTO;

    public function findBySlug(string $slug): SecretariaDTO;

    public function create(array $data): SecretariaDTO;

    public function update(int $id, array $data): SecretariaDTO;

    public function delete(int|array $ids): bool;
}
