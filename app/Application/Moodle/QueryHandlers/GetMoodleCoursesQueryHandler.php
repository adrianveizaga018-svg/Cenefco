<?php

namespace App\Application\Moodle\QueryHandlers;

use App\Application\Moodle\DTOs\MoodleCourseDTO;
use App\Domain\Moodle\Contracts\MoodleCourseRepositoryInterface;

class GetMoodleCoursesQueryHandler
{
    public function __construct(
        private readonly MoodleCourseRepositoryInterface $repository
    ) {}

    public function handle(): array
    {
        return array_map(
            fn ($course) => MoodleCourseDTO::fromArray($course),
            $this->repository->getAll()
        );
    }
}
