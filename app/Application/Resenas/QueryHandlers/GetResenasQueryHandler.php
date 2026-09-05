<?php

namespace App\Application\Resenas\QueryHandlers;

use App\Application\Resenas\Queries\GetResenasQuery;
use App\Domain\Resenas\Contracts\ResenaRepositoryInterface;

class GetResenasQueryHandler
{
    public function __construct(
        private readonly ResenaRepositoryInterface $repository,
    ) {}

    public function handle(GetResenasQuery $query): array
    {
        return $this->repository->paginate($query->pagination, [
            'estado'      => $query->estado,
            'programa_id' => $query->programaId,
        ]);
    }
}
