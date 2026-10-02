<?php

namespace App\Domain\ManualesInstitucionales\Contracts;

use App\Application\ManualesInstitucionales\DTOs\ManualInstitucionalDTO;

interface ManualInstitucionalRepositoryInterface
{
    public function all(): array;

    public function findById(int $id): ManualInstitucionalDTO;

    public function create(array $data): ManualInstitucionalDTO;

    public function update(int $id, array $data): ManualInstitucionalDTO;

    public function delete(int|array $ids): bool;
}
