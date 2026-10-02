<?php

namespace App\Application\Moodle\Handlers;

use App\Application\Moodle\Commands\CreateMoodleCourseCommand;
use App\Application\Moodle\DTOs\MoodleCourseDTO;
use App\Domain\Moodle\Contracts\MoodleCourseRepositoryInterface;

class CreateMoodleCourseHandler
{
    public function __construct(
        private readonly MoodleCourseRepositoryInterface $repository
    ) {}

    public function handle(CreateMoodleCourseCommand $command): MoodleCourseDTO
    {
        $result = $this->repository->create([
            'fullname' => $command->fullname,
            'shortname' => $command->shortname,
            'categoryid' => $command->categoryid,
            'summary' => $command->summary,
            'format' => $command->format,
            'visible' => $command->visible,
            'startdate' => $command->startdate,
            'enddate' => $command->enddate,
            'imageUrl' => $command->imageUrl,
        ]);

        return MoodleCourseDTO::fromArray($result);
    }
}
