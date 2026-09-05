<?php

namespace App\Domain\Horarios\Contracts;

use App\Application\Horarios\DTOs\HorarioDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface HorarioRepositoryInterface
{
    
    public function paginate(PaginationDTO $pagination, bool $conInactivos, ?int $idImp): array;

    public function findById(int $id): HorarioDTO;

    public function create(array $data): HorarioDTO;

    public function update(int $id, array $data): HorarioDTO;

    public function delete(int $id): void;
}
