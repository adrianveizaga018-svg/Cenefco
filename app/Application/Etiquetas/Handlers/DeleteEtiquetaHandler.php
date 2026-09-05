<?php

namespace App\Application\Etiquetas\Handlers;

use App\Application\Etiquetas\Commands\DeleteEtiquetaCommand;
use App\Domain\Etiquetas\Contracts\EtiquetaRepositoryInterface;

class DeleteEtiquetaHandler
{
    public function __construct(private readonly EtiquetaRepositoryInterface $repository) {}

    public function handle(DeleteEtiquetaCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
