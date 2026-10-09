<?php

namespace App\Domain\Organigramas\Contracts;

use App\Application\Organigramas\DTOs\OrganigramaDTO;

interface OrganigramaRepositoryInterface
{
    /** Árbol del organigrama vigente, o null si no hay uno. */
    public function findLatest(): ?array;

    public function findById(int $id): OrganigramaDTO;

    public function create(array $data): OrganigramaDTO;

    public function update(int $id, array $data): OrganigramaDTO;

    public function delete(int|array $ids): bool;

    public function all(): array;
}
