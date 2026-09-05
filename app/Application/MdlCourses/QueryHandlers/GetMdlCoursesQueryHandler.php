<?php

namespace App\Application\MdlCourses\QueryHandlers;

use App\Application\MdlCourses\Queries\GetMdlCoursesQuery;
use App\Domain\MdlCourses\Contracts\MdlCourseRepositoryInterface;

class GetMdlCoursesQueryHandler
{
    public function __construct(private readonly MdlCourseRepositoryInterface $repository) {}

    public function handle(GetMdlCoursesQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->idUsReg, $query->idDocente, $query->conInactivos);
    }
}
