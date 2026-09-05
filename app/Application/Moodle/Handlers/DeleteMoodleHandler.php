<?php

namespace App\Application\Moodle\Handlers;

use App\Application\Moodle\Commands\DeleteMoodleCommand;
use App\Domain\Moodle\Contracts\MoodleRepositoryInterface;

class DeleteMoodleHandler
{
    public function __construct(private readonly MoodleRepositoryInterface $repository) {}

    public function handle(DeleteMoodleCommand $command): void
    {
        $this->repository->delete($command->idMoodle);
    }
}
