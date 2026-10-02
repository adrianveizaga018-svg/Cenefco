<?php

namespace App\Application\Testimonios\Commands;

final readonly class DeleteTestimonioCommand
{
    public function __construct(public int $id) {}
}
