<?php

namespace App\Application\Fotos\QueryHandlers;

use App\Application\Fotos\Queries\GetFotosQuery;
use App\Domain\Fotos\Contracts\FotoRepositoryInterface;

class GetFotosQueryHandler
{
    public function __construct(private readonly FotoRepositoryInterface $repository) {}

    public function handle(GetFotosQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->query, $query->conInactivos);
    }
}
