<?php

namespace App\Domain\HitosInstitucionales\Contracts;

use App\Application\HitosInstitucionales\DTOs\HitoInstitucionalDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface HitoInstitucionalRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array;

    public function findById(int $id): HitoInstitucionalDTO;

    public function create(array $data): HitoInstitucionalDTO;

    public function update(int $id, array $data): HitoInstitucionalDTO;

    public function delete(int|array $ids): bool;
}
