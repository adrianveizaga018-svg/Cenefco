<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\CreateTriviaPreguntaCommand;
use App\Application\Trivia\DTOs\TriviaPreguntaDTO;
use App\Domain\Trivia\Contracts\TriviaPreguntaRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreateTriviaPreguntaHandler
{
    public function __construct(
        private readonly TriviaPreguntaRepositoryInterface $repository
    ) {}

    public function handle(CreateTriviaPreguntaCommand $command): TriviaPreguntaDTO
    {
        return DB::transaction(function () use ($command) {
            return $this->repository->create([
                'categoria_id' => $command->categoria_id,
                'nivel_id' => $command->nivel_id,
                'enunciado' => $command->enunciado,
                'imagen_url' => $command->imagen_url,
                'tiempo_limite_segundos' => $command->tiempo_limite_segundos,
                'activo' => $command->activo,
            ], $command->opciones);
        });
    }
}
