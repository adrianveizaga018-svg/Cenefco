<?php

namespace App\Application\Moodle\QueryHandlers;

use App\Application\Moodle\Queries\GetMoodlesQuery;
use App\Domain\Moodle\Contracts\MoodleRepositoryInterface;

class GetMoodlesQueryHandler
{
    public function __construct(private readonly MoodleRepositoryInterface $repository) {}

    public function handle(GetMoodlesQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->query, $query->conInactivos);
    }
}
