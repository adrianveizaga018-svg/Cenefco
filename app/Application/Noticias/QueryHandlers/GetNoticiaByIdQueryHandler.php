<?php

namespace App\Application\Noticias\QueryHandlers;

use App\Application\Noticias\DTOs\NoticiaDTO;
use App\Application\Noticias\Queries\GetNoticiaByIdQuery;
use App\Domain\Noticias\Contracts\NoticiaRepositoryInterface;

class GetNoticiaByIdQueryHandler
{
    public function __construct(private readonly NoticiaRepositoryInterface $repository) {}

    public function handle(GetNoticiaByIdQuery $query): NoticiaDTO
    {
        return $this->repository->findById($query->id);
    }
}
