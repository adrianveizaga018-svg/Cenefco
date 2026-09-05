<?php

namespace App\Application\Testimonios\Queries;

final readonly class GetTestimonioBySlugQuery
{
    public function __construct(public string $slug) {}
}
