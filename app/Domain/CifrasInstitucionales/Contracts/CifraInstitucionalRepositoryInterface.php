<?php

namespace App\Domain\CifrasInstitucionales\Contracts;

use App\Application\CifrasInstitucionales\DTOs\CifraInstitucionalDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface CifraInstitucionalRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array;

    public function getAllActivas(): array;

    public function findById(int $id): CifraInstitucionalDTO;

    public function create(array $data): CifraInstitucionalDTO;

    public function update(int $id, array $data): CifraInstitucionalDTO;

    public function delete(int|array $ids): bool;
}
