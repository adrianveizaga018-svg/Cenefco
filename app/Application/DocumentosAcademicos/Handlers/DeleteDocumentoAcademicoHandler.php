<?php

namespace App\Application\DocumentosAcademicos\Handlers;

use App\Application\DocumentosAcademicos\Commands\DeleteDocumentoAcademicoCommand;
use App\Domain\DocumentosAcademicos\Contracts\DocumentoAcademicoRepositoryInterface;

class DeleteDocumentoAcademicoHandler
{
    public function __construct(
        private readonly DocumentoAcademicoRepositoryInterface $repository
    ) {}

    public function handle(DeleteDocumentoAcademicoCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
