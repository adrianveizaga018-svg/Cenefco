<?php
namespace App\Application\GaleriasVideos\QueryHandlers;
use App\Application\GaleriasVideos\Queries\GetGaleriasVideosQuery;
use App\Domain\GaleriasVideos\Contracts\GaleriaVideoRepositoryInterface;
class GetGaleriasVideosQueryHandler {
    public function __construct(private readonly GaleriaVideoRepositoryInterface $repository) {}
    public function handle(GetGaleriasVideosQuery $q): array {
        return $this->repository->paginate($q->pagination, $q->soloActivos, $q->soloDestacados);
    }
}
