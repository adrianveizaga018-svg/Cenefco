<?php

namespace App\Domain\Faqs\Contracts;

use App\Application\Faqs\DTOs\FaqDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface FaqRepositoryInterface
{
    public function paginate(PaginationDTO $pagination): array;

    public function findById(int $id): FaqDTO;

    public function create(array $data): FaqDTO;

    public function update(int $id, array $data): FaqDTO;

    public function delete(int|array $ids): bool;
}
