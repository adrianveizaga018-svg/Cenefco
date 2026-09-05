<?php

namespace App\Application\TiposEvento\Handlers;

use App\Application\TiposEvento\Commands\UpdateTipoEventoCommand;
use App\Application\TiposEvento\DTOs\TipoEventoDTO;
use App\Domain\TiposEvento\Contracts\TipoEventoRepositoryInterface;

class UpdateTipoEventoHandler
{
    public function __construct(private readonly TipoEventoRepositoryInterface $repository) {}

    public function handle(UpdateTipoEventoCommand $command): TipoEventoDTO
    {
        return $this->repository->update($command->id, $command->data);
    }
}
