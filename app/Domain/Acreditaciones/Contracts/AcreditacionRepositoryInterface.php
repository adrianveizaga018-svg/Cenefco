<?php

namespace App\Domain\Acreditaciones\Contracts;

use App\Application\Acreditaciones\DTOs\AcreditacionDTO;

interface AcreditacionRepositoryInterface
{
    public function paginate(int $page, int $pageSize, ?string $query, ?bool $activo): array;

    public function findById(int $id): AcreditacionDTO;

    public function create(array $data): AcreditacionDTO;

    public function update(int $id, array $data): AcreditacionDTO;

    public function delete(int $id): void;
}
