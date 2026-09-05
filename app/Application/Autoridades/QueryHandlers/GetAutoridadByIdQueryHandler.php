<?php

namespace App\Application\Autoridades\QueryHandlers;

use App\Application\Autoridades\DTOs\AutoridadDTO;
use App\Application\Autoridades\Queries\GetAutoridadByIdQuery;
use App\Domain\Autoridades\Contracts\AutoridadRepositoryInterface;

class GetAutoridadByIdQueryHandler
{
    public function __construct(private readonly AutoridadRepositoryInterface $repository) {}

    public function handle(GetAutoridadByIdQuery $query): AutoridadDTO
    {
        return $this->repository->findById($query->id);
    }
}
