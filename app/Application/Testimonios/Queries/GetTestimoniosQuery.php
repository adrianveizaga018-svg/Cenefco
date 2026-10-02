<?php

namespace App\Application\Testimonios\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetTestimoniosQuery
{
    public function __construct(
        public PaginationDTO $pagination,
        public bool          $soloDestacados = false,
    ) {}
}
