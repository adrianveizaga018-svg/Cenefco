<?php

namespace App\Application\TiposEvento\Handlers;

use App\Application\TiposEvento\Commands\DeleteTipoEventoCommand;
use App\Domain\TiposEvento\Contracts\TipoEventoRepositoryInterface;

class DeleteTipoEventoHandler
{
    public function __construct(private readonly TipoEventoRepositoryInterface $repository) {}

    public function handle(DeleteTipoEventoCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
