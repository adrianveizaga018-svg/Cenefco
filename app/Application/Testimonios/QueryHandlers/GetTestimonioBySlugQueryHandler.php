<?php

namespace App\Application\Testimonios\QueryHandlers;

use App\Application\Testimonios\DTOs\TestimonioDTO;
use App\Application\Testimonios\Queries\GetTestimonioBySlugQuery;
use App\Domain\Testimonios\Contracts\TestimonioRepositoryInterface;

class GetTestimonioBySlugQueryHandler
{
    public function __construct(private readonly TestimonioRepositoryInterface $repository) {}

    public function handle(GetTestimonioBySlugQuery $query): TestimonioDTO
    {
        return $this->repository->findBySlug($query->slug);
    }
}
