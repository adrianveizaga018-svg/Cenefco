<?php

namespace App\Application\MdlCourses\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetMdlCoursesQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?int $idUsReg,
        public ?int $idDocente,
        public bool $conInactivos,
    ) {}
}
