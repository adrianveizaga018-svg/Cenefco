<?php

namespace App\Application\MdlCourses\Handlers;

use App\Application\MdlCourses\Commands\DeleteMdlCourseCommand;
use App\Domain\MdlCourses\Contracts\MdlCourseRepositoryInterface;

class DeleteMdlCourseHandler
{
    public function __construct(private readonly MdlCourseRepositoryInterface $repository) {}

    public function handle(DeleteMdlCourseCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
