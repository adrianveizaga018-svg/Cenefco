<?php

namespace App\Application\Faqs\Handlers;

use App\Application\Faqs\Commands\DeleteFaqCommand;
use App\Domain\Faqs\Contracts\FaqRepositoryInterface;

class DeleteFaqHandler
{
    public function __construct(private readonly FaqRepositoryInterface $repository) {}

    public function handle(DeleteFaqCommand $c): void
    {
        $this->repository->delete($c->id);
    }
}
