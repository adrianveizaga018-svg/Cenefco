<?php

namespace App\Domain\Moodle\Contracts;

use App\Application\Moodle\DTOs\MoodleDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface MoodleRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?string $query, bool $conInactivos): array;
    public function findById(int $id): object;
    public function create(array $data): object;
    public function update(int $id, array $data): void;
    public function delete(int $id): void;
}
