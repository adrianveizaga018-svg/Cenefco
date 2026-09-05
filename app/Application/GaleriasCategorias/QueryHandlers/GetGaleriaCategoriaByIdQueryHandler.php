<?php
namespace App\Application\GaleriasCategorias\QueryHandlers;
use App\Application\GaleriasCategorias\DTOs\GaleriaCategoriaDTO;
use App\Application\GaleriasCategorias\Queries\GetGaleriaCategoriaByIdQuery;
use App\Domain\GaleriasCategorias\Contracts\GaleriaCategoriaRepositoryInterface;
class GetGaleriaCategoriaByIdQueryHandler {
    public function __construct(private readonly GaleriaCategoriaRepositoryInterface $repository) {}
    public function handle(GetGaleriaCategoriaByIdQuery $q): GaleriaCategoriaDTO { return $this->repository->findById($q->id); }
}
