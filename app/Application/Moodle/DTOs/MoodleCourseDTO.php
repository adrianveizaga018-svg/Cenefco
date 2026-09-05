<?php

namespace App\Application\Moodle\DTOs;

final readonly class MoodleCourseDTO
{
    public function __construct(
        public int $id,
        public string $fullname,
        public string $shortname,
        public int $categoryid,
        public string $summary,
        public int $visible,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            fullname: $data['fullname'] ?? '',
            shortname: $data['shortname'] ?? '',
            categoryid: $data['categoryid'] ?? 1,
            summary: strip_tags($data['summary'] ?? ''),
            visible: $data['visible'] ?? 1,
        );
    }
}
