<?php

namespace App\Application\Usuarios\QueryHandlers;

use App\Application\Usuarios\Queries\GetUsuariosQuery;
use App\Domain\Usuarios\Contracts\UserRepositoryInterface;

class GetUsuariosQueryHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository
    ) {}

    public function handle(GetUsuariosQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->filters);
    }
}
