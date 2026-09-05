<?php

namespace App\Application\MdlUsers\QueryHandlers;

use App\Application\MdlUsers\DTOs\MdlUserDTO;
use App\Application\MdlUsers\Queries\GetMdlUserByIdQuery;
use App\Domain\MdlUsers\Contracts\MdlUserRepositoryInterface;

class GetMdlUserByIdQueryHandler
{
    public function __construct(private readonly MdlUserRepositoryInterface $repository) {}

    public function handle(GetMdlUserByIdQuery $query): MdlUserDTO
    {
        return MdlUserDTO::fromRow($this->repository->findById($query->id));
    }
}
