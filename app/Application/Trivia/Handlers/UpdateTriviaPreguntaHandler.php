<?php

namespace App\Application\Trivia\Handlers;

use App\Application\Trivia\Commands\UpdateTriviaPreguntaCommand;
use App\Application\Trivia\DTOs\TriviaPreguntaDTO;
use App\Domain\Trivia\Contracts\TriviaPreguntaRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UpdateTriviaPreguntaHandler
{
    public function __construct(
        private readonly TriviaPreguntaRepositoryInterface $repository
    ) {}

    public function handle(UpdateTriviaPreguntaCommand $command): TriviaPreguntaDTO
    {
        return DB::transaction(function () use ($command) {
            return $this->repository->update($command->id, $command->data, $command->opciones);
        });
    }
}
