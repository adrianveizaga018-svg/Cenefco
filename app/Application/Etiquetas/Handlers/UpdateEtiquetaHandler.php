<?php

namespace App\Application\Etiquetas\Handlers;

use App\Application\Etiquetas\Commands\UpdateEtiquetaCommand;
use App\Application\Etiquetas\DTOs\EtiquetaDTO;
use App\Domain\Etiquetas\Contracts\EtiquetaRepositoryInterface;

class UpdateEtiquetaHandler
{
    public function __construct(private readonly EtiquetaRepositoryInterface $repository) {}

    public function handle(UpdateEtiquetaCommand $command): EtiquetaDTO
    {
        $data = array_filter([
            'nombre' => $command->nombre,
            'color'  => $command->color,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
