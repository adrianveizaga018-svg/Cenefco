<?php

namespace App\Application\Acreditaciones\QueryHandlers;

use App\Application\Acreditaciones\DTOs\AcreditacionDTO;
use App\Application\Acreditaciones\Queries\GetAcreditacionByIdQuery;
use App\Domain\Acreditaciones\Contracts\AcreditacionRepositoryInterface;

class GetAcreditacionByIdQueryHandler
{
    public function __construct(
        private readonly AcreditacionRepositoryInterface $repository,
    ) {}

    public function handle(GetAcreditacionByIdQuery $query): AcreditacionDTO
    {
        return $this->repository->findById($query->id);
    }
}
