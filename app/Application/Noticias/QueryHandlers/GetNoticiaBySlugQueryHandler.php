<?php

namespace App\Application\Noticias\QueryHandlers;

use App\Application\Noticias\DTOs\NoticiaDTO;
use App\Application\Noticias\Queries\GetNoticiaBySlugQuery;
use App\Domain\Noticias\Contracts\NoticiaRepositoryInterface;

class GetNoticiaBySlugQueryHandler
{
    public function __construct(private readonly NoticiaRepositoryInterface $repository) {}

    public function handle(GetNoticiaBySlugQuery $query): NoticiaDTO
    {
        return $this->repository->findBySlug($query->slug);
    }
}
