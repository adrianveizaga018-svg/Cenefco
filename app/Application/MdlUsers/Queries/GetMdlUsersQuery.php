<?php

namespace App\Application\MdlUsers\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetMdlUsersQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string $query,
        public ?int $idUsReg,
        public bool $conInactivos,
    ) {}
}
