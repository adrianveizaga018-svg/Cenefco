<?php

namespace App\Application\MdlCourses\QueryHandlers;

use App\Application\MdlCourses\DTOs\MdlCourseDTO;
use App\Application\MdlCourses\Queries\GetMdlCourseByIdQuery;
use App\Domain\MdlCourses\Contracts\MdlCourseRepositoryInterface;

class GetMdlCourseByIdQueryHandler
{
    public function __construct(private readonly MdlCourseRepositoryInterface $repository) {}

    public function handle(GetMdlCourseByIdQuery $query): MdlCourseDTO
    {
        return MdlCourseDTO::fromRow($this->repository->findById($query->id));
    }
}
