<?php
namespace App\Application\GaleriasCategorias\QueryHandlers;
use App\Application\GaleriasCategorias\Queries\GetGaleriasCategoriasQuery;
use App\Domain\GaleriasCategorias\Contracts\GaleriaCategoriaRepositoryInterface;
class GetGaleriasCategoriasQueryHandler {
    public function __construct(private readonly GaleriaCategoriaRepositoryInterface $repository) {}
    public function handle(GetGaleriasCategoriasQuery $q): array {
        return $this->repository->paginate($q->pagination, $q->soloActivos);
    }
}
