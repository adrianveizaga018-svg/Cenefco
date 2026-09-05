<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\CreateTriviaPremioCommand;
use App\Application\Trivia\DTOs\TriviaPremioDTO;
use App\Domain\Trivia\Contracts\TriviaPremioRepositoryInterface;

class CreateTriviaPremioHandler
{
    public function __construct(
        private readonly TriviaPremioRepositoryInterface $repository
    ) {}

    public function handle(CreateTriviaPremioCommand $command): TriviaPremioDTO
    {
        $model = $this->repository->create([
            'nombre' => $command->nombre,
            'descripcion' => $command->descripcion,
            'tipo' => $command->tipo,
            'imagen_url' => $command->imagen_url,
            'costo_puntos' => $command->costo_puntos,
            'stock' => $command->stock,
            'activo' => $command->activo,
            'orden' => $command->orden,
        ]);

        return TriviaPremioDTO::fromModel($model);
    }
}
