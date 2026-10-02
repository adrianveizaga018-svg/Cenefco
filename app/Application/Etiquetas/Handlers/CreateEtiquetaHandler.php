<?php

namespace App\Application\Etiquetas\Handlers;

use App\Application\Etiquetas\Commands\CreateEtiquetaCommand;
use App\Application\Etiquetas\DTOs\EtiquetaDTO;
use App\Domain\Etiquetas\Contracts\EtiquetaRepositoryInterface;

class CreateEtiquetaHandler
{
    public function __construct(private readonly EtiquetaRepositoryInterface $repository) {}

    public function handle(CreateEtiquetaCommand $command): EtiquetaDTO
    {
        return $this->repository->create(['nombre' => $command->nombre]);
    }
}
