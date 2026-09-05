<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\CreateTriviaNivelCommand;
use App\Application\Trivia\DTOs\TriviaNivelDTO;
use App\Domain\Trivia\Contracts\TriviaNivelRepositoryInterface;

class CreateTriviaNivelHandler
{
    public function __construct(
        private readonly TriviaNivelRepositoryInterface $repository
    ) {}

    public function handle(CreateTriviaNivelCommand $command): TriviaNivelDTO
    {
        return $this->repository->create([
            'categoria_id' => $command->categoria_id,
            'nombre' => $command->nombre,
            'orden' => $command->orden,
            'puntaje_base' => $command->puntaje_base,
            'activo' => $command->activo,
        ]);
    }
}
