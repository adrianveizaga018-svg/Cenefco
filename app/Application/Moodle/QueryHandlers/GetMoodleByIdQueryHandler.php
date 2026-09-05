<?php

namespace App\Application\Moodle\QueryHandlers;

use App\Application\Moodle\DTOs\MoodleDTO;
use App\Application\Moodle\Queries\GetMoodleByIdQuery;
use App\Domain\Moodle\Contracts\MoodleRepositoryInterface;

class GetMoodleByIdQueryHandler
{
    public function __construct(private readonly MoodleRepositoryInterface $repository) {}

    public function handle(GetMoodleByIdQuery $query): MoodleDTO
    {
        return MoodleDTO::fromRow($this->repository->findById($query->idMoodle));
    }
}
