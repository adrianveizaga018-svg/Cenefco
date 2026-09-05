<?php

namespace App\Application\Faqs\QueryHandlers;

use App\Application\Faqs\Queries\GetFaqsQuery;
use App\Domain\Faqs\Contracts\FaqRepositoryInterface;

class GetFaqsQueryHandler
{
    public function __construct(private readonly FaqRepositoryInterface $repository) {}

    public function handle(GetFaqsQuery $q): array
    {
        return $this->repository->paginate($q->pagination);
    }
}
