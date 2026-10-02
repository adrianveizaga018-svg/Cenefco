<?php

namespace App\Application\Faqs\QueryHandlers;

use App\Application\Faqs\DTOs\FaqDTO;
use App\Application\Faqs\Queries\GetFaqByIdQuery;
use App\Domain\Faqs\Contracts\FaqRepositoryInterface;

class GetFaqByIdQueryHandler
{
    public function __construct(private readonly FaqRepositoryInterface $repository) {}

    public function handle(GetFaqByIdQuery $q): FaqDTO
    {
        return $this->repository->findById($q->id);
    }
}
