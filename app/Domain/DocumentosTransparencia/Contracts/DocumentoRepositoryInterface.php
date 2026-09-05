<?php

namespace App\Domain\DocumentosTransparencia\Contracts;

use App\Application\DocumentosTransparencia\DTOs\DocumentoDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface DocumentoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array;

    public function findById(int $id): DocumentoDTO;

    public function findBySlug(string $slug): DocumentoDTO;

    public function create(array $data): DocumentoDTO;

    public function update(int $id, array $data): DocumentoDTO;

    public function delete(int|array $ids): bool;
}
