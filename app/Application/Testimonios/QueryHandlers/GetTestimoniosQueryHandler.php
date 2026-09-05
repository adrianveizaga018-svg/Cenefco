<?php

namespace App\Application\Testimonios\QueryHandlers;

use App\Application\Testimonios\Queries\GetTestimoniosQuery;
use App\Domain\Testimonios\Contracts\TestimonioRepositoryInterface;

class GetTestimoniosQueryHandler
{
    public function __construct(private readonly TestimonioRepositoryInterface $repository) {}

    public function handle(GetTestimoniosQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->soloDestacados);
    }
}
