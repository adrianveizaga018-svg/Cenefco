<?php

namespace App\Domain\BannersPortal\Contracts;

use App\Application\BannersPortal\DTOs\BannerPortalDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface BannerPortalRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloActivos = false): array;

    public function findById(int $id): BannerPortalDTO;

    public function findBySlug(string $slug): BannerPortalDTO;

    public function create(array $data): BannerPortalDTO;

    public function update(int $id, array $data): BannerPortalDTO;

    public function delete(int|array $ids): bool;
}
