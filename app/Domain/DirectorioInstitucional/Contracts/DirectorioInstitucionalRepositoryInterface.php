<?php

namespace App\Domain\DirectorioInstitucional\Contracts;

use App\Application\DirectorioInstitucional\DTOs\DirectorioInstitucionalDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface DirectorioInstitucionalRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array;

    public function findById(int $id): DirectorioInstitucionalDTO;

    public function create(array $data): DirectorioInstitucionalDTO;

    public function update(int $id, array $data): DirectorioInstitucionalDTO;

    public function delete(int|array $ids): bool;
}
