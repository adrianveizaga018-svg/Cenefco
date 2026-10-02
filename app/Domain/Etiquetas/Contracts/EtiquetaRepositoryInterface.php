<?php

namespace App\Domain\Etiquetas\Contracts;

use App\Application\Etiquetas\DTOs\EtiquetaDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface EtiquetaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array;

    public function findById(int $id): EtiquetaDTO;

    public function findBySlug(string $slug): EtiquetaDTO;

    public function create(array $data): EtiquetaDTO;

    public function update(int $id, array $data): EtiquetaDTO;

    public function delete(int|array $ids): bool;
}
