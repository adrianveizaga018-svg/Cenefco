<?php

namespace App\Domain\MdlCourses\Contracts;

use App\Application\MdlCourses\DTOs\MdlCourseDTO;
use App\Shared\Kernel\DTOs\PaginationDTO;

interface MdlCourseRepositoryInterface
{
    public function paginate(PaginationDTO $pagination, ?int $idUsReg, ?int $idDocente, bool $conInactivos): array;
    public function findById(int $id): object;
    public function create(array $data): object;
    public function update(int $id, array $data): void;
    public function delete(int $id): void;
}
