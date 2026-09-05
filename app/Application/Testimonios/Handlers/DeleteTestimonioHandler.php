<?php

namespace App\Application\Testimonios\Handlers;

use App\Application\Testimonios\Commands\DeleteTestimonioCommand;
use App\Domain\Testimonios\Contracts\TestimonioRepositoryInterface;

class DeleteTestimonioHandler
{
    public function __construct(private readonly TestimonioRepositoryInterface $repository) {}

    public function handle(DeleteTestimonioCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
