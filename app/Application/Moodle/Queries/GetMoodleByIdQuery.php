<?php

namespace App\Application\Moodle\Queries;

final readonly class GetMoodleByIdQuery
{
    public function __construct(public int $idMoodle) {}
}
