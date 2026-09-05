<?php

namespace App\Application\TiposDocumentoTransparencia\Handlers;

use App\Application\TiposDocumentoTransparencia\Commands\CreateTipoDocumentoTransparenciaCommand;
use App\Application\TiposDocumentoTransparencia\DTOs\TipoDocumentoTransparenciaDTO;
use App\Domain\TiposDocumentoTransparencia\Contracts\TipoDocumentoTransparenciaRepositoryInterface;

class CreateTipoDocumentoTransparenciaHandler
{
    public function __construct(private readonly TipoDocumentoTransparenciaRepositoryInterface $repository) {}

    public function handle(CreateTipoDocumentoTransparenciaCommand $command): TipoDocumentoTransparenciaDTO
    {
        return $this->repository->create([
            'nombre' => $command->nombre,
            'activo' => $command->activo,
        ]);
    }
}
