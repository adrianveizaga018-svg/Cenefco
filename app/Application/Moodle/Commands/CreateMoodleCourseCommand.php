<?php

namespace App\Application\Moodle\Commands;

final readonly class CreateMoodleCourseCommand
{
    public function __construct(
        public string $fullname,
        public string $shortname,
        public int $categoryid = 1,
        public string $summary = '',
        public string $format = 'topics',
        public int $visible = 1,
        public int $startdate = 0,
        public int $enddate = 0,
        public ?string $imageUrl = null,
    ) {}
}
