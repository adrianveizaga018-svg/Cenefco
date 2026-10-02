<?php
namespace App\Application\GaleriasCategorias\Handlers;
use App\Application\GaleriasCategorias\Commands\UpdateGaleriaCategoriaCommand;
use App\Application\GaleriasCategorias\DTOs\GaleriaCategoriaDTO;
use App\Domain\GaleriasCategorias\Contracts\GaleriaCategoriaRepositoryInterface;
class UpdateGaleriaCategoriaHandler {
    public function __construct(private readonly GaleriaCategoriaRepositoryInterface $repository) {}
    public function handle(UpdateGaleriaCategoriaCommand $c): GaleriaCategoriaDTO {
        $data = array_filter([
            'nombre' => $c->nombre,
            'descripcion' => $c->descripcion,
            'orden' => $c->orden,
            'activo' => $c->activo,
        ], fn($v) => $v !== null);
        return $this->repository->update($c->id, $data);
    }
}
