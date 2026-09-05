<?php

namespace App\Application\TiposDocumentoTransparencia\Handlers;

use App\Application\TiposDocumentoTransparencia\Commands\UpdateTipoDocumentoTransparenciaCommand;
use App\Application\TiposDocumentoTransparencia\DTOs\TipoDocumentoTransparenciaDTO;
use App\Domain\TiposDocumentoTransparencia\Contracts\TipoDocumentoTransparenciaRepositoryInterface;

class UpdateTipoDocumentoTransparenciaHandler
{
    public function __construct(private readonly TipoDocumentoTransparenciaRepositoryInterface $repository) {}

    public function handle(UpdateTipoDocumentoTransparenciaCommand $command): TipoDocumentoTransparenciaDTO
    {
        return $this->repository->update($command->id, $command->data);
    }
}
