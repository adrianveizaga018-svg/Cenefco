<?php

namespace App\Application\MdlUsers\QueryHandlers;

use App\Application\MdlUsers\Queries\GetMdlUsersQuery;
use App\Domain\MdlUsers\Contracts\MdlUserRepositoryInterface;

class GetMdlUsersQueryHandler
{
    public function __construct(private readonly MdlUserRepositoryInterface $repository) {}

    public function handle(GetMdlUsersQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->query, $query->idUsReg, $query->conInactivos);
    }
}
