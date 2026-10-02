<?php
namespace App\Domain\GaleriasVideos\Contracts;
use App\Application\GaleriasVideos\DTOs\GaleriaVideoDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;
interface GaleriaVideoRepositoryInterface {
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false, bool $soloDestacados = false): array;
    public function findById(int $id): GaleriaVideoDTO;
    public function create(array $data): GaleriaVideoDTO;
    public function update(int $id, array $data): GaleriaVideoDTO;
    public function delete(int|array $ids): bool;
}
