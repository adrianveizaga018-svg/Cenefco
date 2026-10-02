<?php

namespace App\Domain\Moodle\Contracts;

interface MoodleCourseRepositoryInterface
{
    public function create(array $data): array;

    public function getAll(): array;

    public function getById(int $id): array;

    public function delete(int $id): void;
}
