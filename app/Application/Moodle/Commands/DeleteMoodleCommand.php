<?php

namespace App\Application\Moodle\Commands;

final readonly class DeleteMoodleCommand
{
    public function __construct(public int $idMoodle) {}
}
