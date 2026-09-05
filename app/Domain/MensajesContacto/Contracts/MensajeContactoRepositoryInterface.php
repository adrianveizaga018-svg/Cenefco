<?php

namespace App\Domain\MensajesContacto\Contracts;

use App\Application\MensajesContacto\DTOs\MensajeContactoDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface MensajeContactoRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array;

    public function findById(int $id): MensajeContactoDTO;

    public function create(array $data): MensajeContactoDTO;

    public function update(int $id, array $data): MensajeContactoDTO;

    public function delete(int|array $ids): bool;
}
