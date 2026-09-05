<?php

namespace App\Application\EventosFotos\Handlers;

use App\Application\EventosFotos\Commands\UpdateEventoFotoCommand;
use App\Application\EventosFotos\DTOs\EventoFotoDTO;
use App\Domain\EventosFotos\Contracts\EventoFotoRepositoryInterface;

class UpdateEventoFotoHandler
{
    public function __construct(private readonly EventoFotoRepositoryInterface $repository) {}

    public function handle(UpdateEventoFotoCommand $command): EventoFotoDTO
    {
        $data = array_filter([
            'archivo_url' => $command->archivo_url,
            'titulo'      => $command->titulo,
            'orden'       => $command->orden,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
