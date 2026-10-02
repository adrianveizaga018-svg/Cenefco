<?php

namespace App\Domain\Trivia\Contracts;

use App\Application\Trivia\DTOs\TriviaPreguntaDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface TriviaPreguntaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, array $filters = []): array;

    public function findById(int $id): TriviaPreguntaDTO;

    public function create(array $data, array $opciones): TriviaPreguntaDTO;

    public function update(int $id, array $data, ?array $opciones = null): TriviaPreguntaDTO;

    public function delete(int|array $ids): bool;

    public function siguienteParaJuego(int $categoriaId, array $excluirIds): ?TriviaPreguntaDTO;

    public function seleccionarParaDuelo(int $categoriaId, int $cantidad): array;
}
