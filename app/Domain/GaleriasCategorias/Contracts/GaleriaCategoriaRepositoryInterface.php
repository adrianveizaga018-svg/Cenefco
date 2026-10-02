<?php
namespace App\Domain\GaleriasCategorias\Contracts;
use App\Application\GaleriasCategorias\DTOs\GaleriaCategoriaDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;
interface GaleriaCategoriaRepositoryInterface {
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array;
    public function findById(int $id): GaleriaCategoriaDTO;
    public function findBySlug(string $slug): GaleriaCategoriaDTO;
    public function create(array $data): GaleriaCategoriaDTO;
    public function update(int $id, array $data): GaleriaCategoriaDTO;
    public function delete(int|array $ids): bool;
}
