<?php

namespace App\Application\Fotos\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetFotosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $query,
        public bool          $conInactivos,
    ) {}
}
