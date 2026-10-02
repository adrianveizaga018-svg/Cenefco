<?php

namespace App\Application\Boletines\QueryHandlers;

use App\Application\Boletines\DTOs\BoletinDTO;
use App\Application\Boletines\Queries\GetBoletinBySlugQuery;
use App\Domain\Boletines\Contracts\BoletinRepositoryInterface;

class GetBoletinBySlugQueryHandler
{
    public function __construct(private readonly BoletinRepositoryInterface $repository) {}

    public function handle(GetBoletinBySlugQuery $query): BoletinDTO
    {
        return BoletinDTO::fromRow($this->repository->findBySlug($query->slug));
    }
}
