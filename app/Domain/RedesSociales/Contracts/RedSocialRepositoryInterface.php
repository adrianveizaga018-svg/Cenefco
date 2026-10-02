<?php

namespace App\Domain\RedesSociales\Contracts;

use App\Application\RedesSociales\DTOs\RedSocialDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface RedSocialRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array;

    public function findById(int $id): RedSocialDTO;

    public function create(array $data): RedSocialDTO;

    public function update(int $id, array $data): RedSocialDTO;

    public function delete(int|array $ids): bool;
}
