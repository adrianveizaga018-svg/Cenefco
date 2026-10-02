<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\UpdateTriviaPremioCommand;
use App\Application\Trivia\DTOs\TriviaPremioDTO;
use App\Domain\Trivia\Contracts\TriviaPremioRepositoryInterface;
use App\Domain\Trivia\Exceptions\TriviaPremioNotFoundException;

class UpdateTriviaPremioHandler
{
    public function __construct(
        private readonly TriviaPremioRepositoryInterface $repository
    ) {}

    public function handle(UpdateTriviaPremioCommand $command): TriviaPremioDTO
    {
        if (! $this->repository->findById($command->id)) {
            throw new TriviaPremioNotFoundException($command->id);
        }

        $model = $this->repository->update($command->id, [
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
