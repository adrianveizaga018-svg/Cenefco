<?php

namespace App\Domain\Testimonios\Contracts;

use App\Application\Testimonios\DTOs\TestimonioDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface TestimonioRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, bool $soloDestacados = false): array;

    public function findById(int $id): TestimonioDTO;

    public function findBySlug(string $slug): TestimonioDTO;

    public function create(array $data): TestimonioDTO;

    public function update(int $id, array $data): TestimonioDTO;

    public function delete(int|array $ids): bool;
}
