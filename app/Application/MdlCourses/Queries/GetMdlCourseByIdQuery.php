<?php

namespace App\Application\MdlCourses\Queries;

final readonly class GetMdlCourseByIdQuery
{
    public function __construct(public int $id) {}
}
