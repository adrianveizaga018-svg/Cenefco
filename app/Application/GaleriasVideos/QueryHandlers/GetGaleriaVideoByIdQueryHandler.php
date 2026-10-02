<?php
namespace App\Application\GaleriasVideos\QueryHandlers;
use App\Application\GaleriasVideos\DTOs\GaleriaVideoDTO;
use App\Application\GaleriasVideos\Queries\GetGaleriaVideoByIdQuery;
use App\Domain\GaleriasVideos\Contracts\GaleriaVideoRepositoryInterface;
class GetGaleriaVideoByIdQueryHandler {
    public function __construct(private readonly GaleriaVideoRepositoryInterface $repository) {}
    public function handle(GetGaleriaVideoByIdQuery $q): GaleriaVideoDTO { return $this->repository->findById($q->id); }
}
