<?php

namespace App\Domain\HistoriaMunicipio\Contracts;

use App\Application\HistoriaMunicipio\DTOs\HistoriaMunicipioDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface HistoriaMunicipioRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array;

    public function findById(int $id): HistoriaMunicipioDTO;

    public function create(array $data): HistoriaMunicipioDTO;

    public function update(int $id, array $data): HistoriaMunicipioDTO;

    public function delete(int|array $ids): bool;
}
