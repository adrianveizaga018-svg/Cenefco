<?php

namespace App\Domain\EventosFotos\Contracts;

use App\Application\EventosFotos\DTOs\EventoFotoDTO;

interface EventoFotoRepositoryInterface
{
    public function findByEvento(int $eventoId): array;

    public function findById(int $id): EventoFotoDTO;

    public function create(array $data): EventoFotoDTO;

    public function update(int $id, array $data): EventoFotoDTO;

    public function delete(int|array $ids): bool;
}
