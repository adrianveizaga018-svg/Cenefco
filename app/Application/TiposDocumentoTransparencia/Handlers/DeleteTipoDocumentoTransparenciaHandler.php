<?php

namespace App\Application\TiposDocumentoTransparencia\Handlers;

use App\Application\TiposDocumentoTransparencia\Commands\DeleteTipoDocumentoTransparenciaCommand;
use App\Domain\TiposDocumentoTransparencia\Contracts\TipoDocumentoTransparenciaRepositoryInterface;

class DeleteTipoDocumentoTransparenciaHandler
{
    public function __construct(private readonly TipoDocumentoTransparenciaRepositoryInterface $repository) {}

    public function handle(DeleteTipoDocumentoTransparenciaCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
