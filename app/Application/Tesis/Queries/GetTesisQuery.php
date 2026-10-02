<?php

namespace App\Application\Tesis\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetTesisQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public ?string       $query,
        public ?int          $tipoTesis,
        public bool          $conInactivos,
    ) {}
}
