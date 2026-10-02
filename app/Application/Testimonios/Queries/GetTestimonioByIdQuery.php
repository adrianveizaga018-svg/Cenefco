<?php

namespace App\Application\Testimonios\Queries;

final readonly class GetTestimonioByIdQuery
{
    public function __construct(public int $id) {}
}
