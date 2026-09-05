<?php

namespace App\Domain\Noticias\Contracts;

use App\Application\Noticias\DTOs\NoticiaDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface NoticiaRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array;

    public function findById(int $id): NoticiaDTO;

    public function findBySlug(string $slug): NoticiaDTO;

    public function create(array $data): NoticiaDTO;

    public function update(int $id, array $data): NoticiaDTO;

    public function delete(int|array $ids): bool;

    public function syncEtiquetas(int $noticiaId, array $etiquetaIds): void;
}
