<?php

namespace App\Application\Testimonios\QueryHandlers;

use App\Application\Testimonios\DTOs\TestimonioDTO;
use App\Application\Testimonios\Queries\GetTestimonioByIdQuery;
use App\Domain\Testimonios\Contracts\TestimonioRepositoryInterface;

class GetTestimonioByIdQueryHandler
{
    public function __construct(private readonly TestimonioRepositoryInterface $repository) {}

    public function handle(GetTestimonioByIdQuery $query): TestimonioDTO
    {
        return $this->repository->findById($query->id);
    }
}
