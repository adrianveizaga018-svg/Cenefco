<?php
namespace App\Application\GaleriasCategorias\Handlers;
use App\Application\GaleriasCategorias\Commands\DeleteGaleriaCategoriaCommand;
use App\Domain\GaleriasCategorias\Contracts\GaleriaCategoriaRepositoryInterface;
class DeleteGaleriaCategoriaHandler {
    public function __construct(private readonly GaleriaCategoriaRepositoryInterface $repository) {}
    public function handle(DeleteGaleriaCategoriaCommand $c): void { $this->repository->delete($c->id); }
}
