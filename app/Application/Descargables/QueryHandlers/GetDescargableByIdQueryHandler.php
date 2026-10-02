<?php

declare(strict_types=1);

namespace App\Application\Descargables\QueryHandlers;

use App\Application\Descargables\DTOs\DescargableDTO;
use App\Application\Descargables\Queries\GetDescargableByIdQuery;
use App\Domain\Descargables\Contracts\DescargableRepositoryInterface;

class GetDescargableByIdQueryHandler
{
    public function __construct(
        private readonly DescargableRepositoryInterface $repository,
    ) {}

    public function handle(GetDescargableByIdQuery $query): DescargableDTO
    {
        return $this->repository->findById($query->id);
    }
}
