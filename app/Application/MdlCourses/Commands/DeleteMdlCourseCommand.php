<?php

namespace App\Application\MdlCourses\Commands;

final readonly class DeleteMdlCourseCommand
{
    public function __construct(public int $id) {}
}
