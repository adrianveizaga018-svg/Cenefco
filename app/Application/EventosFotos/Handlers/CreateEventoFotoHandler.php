<?php

namespace App\Application\EventosFotos\Handlers;

use App\Application\EventosFotos\Commands\CreateEventoFotoCommand;
use App\Application\EventosFotos\DTOs\EventoFotoDTO;
use App\Domain\EventosFotos\Contracts\EventoFotoRepositoryInterface;

class CreateEventoFotoHandler
{
    public function __construct(private readonly EventoFotoRepositoryInterface $repository) {}

    public function handle(CreateEventoFotoCommand $command): EventoFotoDTO
    {
        return $this->repository->create([
            'evento_id' => $command->evento_id,
            'archivo_url' => $command->archivo_url,
            'titulo' => $command->titulo,
            'orden' => $command->orden,
        ]);
    }
}
