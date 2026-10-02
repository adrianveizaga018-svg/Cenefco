<?php

namespace App\Domain\PreguntasFrecuentes\Contracts;

use App\Application\PreguntasFrecuentes\DTOs\PreguntaFrecuenteDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface PreguntaFrecuenteRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array;

    public function findById(int $id): PreguntaFrecuenteDTO;

    public function create(array $data): PreguntaFrecuenteDTO;

    public function update(int $id, array $data): PreguntaFrecuenteDTO;

    public function delete(int|array $ids): bool;
}
