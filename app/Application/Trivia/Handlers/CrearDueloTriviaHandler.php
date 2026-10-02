<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\CrearDueloTriviaCommand;
use App\Application\Trivia\DTOs\TriviaDueloEstadoDTO;
use App\Application\Trivia\Services\TriviaDueloEstadoService;
use App\Domain\Trivia\Contracts\TriviaPartidaRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CrearDueloTriviaHandler
{
    public function __construct(
        private readonly TriviaPartidaRepositoryInterface $partidaRepository,
        private readonly TriviaDueloEstadoService $estadoService,
    ) {}

    public function handle(CrearDueloTriviaCommand $command): TriviaDueloEstadoDTO
    {
        return DB::transaction(function () use ($command) {
            $jugador = $this->partidaRepository->crearDuelo($command->categoriaId, $command->usuarioId);

            return $this->estadoService->construir($jugador->partida_id, $command->usuarioId);
        });
    }
}
