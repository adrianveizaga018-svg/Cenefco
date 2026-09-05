<?php
namespace App\Application\GaleriasVideos\Handlers;
use App\Application\GaleriasVideos\Commands\DeleteGaleriaVideoCommand;
use App\Domain\GaleriasVideos\Contracts\GaleriaVideoRepositoryInterface;
class DeleteGaleriaVideoHandler {
    public function __construct(private readonly GaleriaVideoRepositoryInterface $repository) {}
    public function handle(DeleteGaleriaVideoCommand $c): void { $this->repository->delete($c->id); }
}
