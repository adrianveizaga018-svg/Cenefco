<?php
namespace App\Application\GaleriasCategorias\Handlers;
use App\Application\GaleriasCategorias\Commands\CreateGaleriaCategoriaCommand;
use App\Application\GaleriasCategorias\DTOs\GaleriaCategoriaDTO;
use App\Domain\GaleriasCategorias\Contracts\GaleriaCategoriaRepositoryInterface;
class CreateGaleriaCategoriaHandler {
    public function __construct(private readonly GaleriaCategoriaRepositoryInterface $repository) {}
    public function handle(CreateGaleriaCategoriaCommand $c): GaleriaCategoriaDTO {
        return $this->repository->create([
            'nombre' => $c->nombre,
            'descripcion' => $c->descripcion,
            'orden' => $c->orden,
            'activo' => $c->activo,
        ]);
    }
}
